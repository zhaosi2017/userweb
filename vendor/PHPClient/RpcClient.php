<?php
namespace PHPClient;

use \Exception;

require_once __DIR__.'/../MNLogger/Base.php';
require_once __DIR__.'/../MNLogger/TraceLogger.php';

/**
 *
 * 版本：1.2.9
 * 发布日期：2015-05-18
 * 特性更新：
 * 2015-05-18 fix Text.php recover bad address and fix a notice
 * 2015-04-27 修复由于同时使用Thrift客户端无法踢掉故障节点bug
 * 2015-03-31 当无法从内存读取故障列表时，一定几率删除共享内存
 * 2015-03-13 去掉__call最后一个参数可能是回调的机制
 * 2015-03-11 支持链接超时设置，支持告警概率设置，优化锁机制避免死锁，去掉配置md5检验，增加共享内存大小，自动加载回调前置，解决由于写共享内存coredump问题，告警文件锁权限设置，区分接收超时及链接断开错误，remoteCall增加参数方便查看调用栈
 * 2015-02-28 为kb临时关闭踢出故障节点功能
 * 2015-02-27 链接超时时间改成4秒
 * 2015-02-10 public static $smsAlarmPrarm
 * 2015-01-27 增加超时时间配置，默认23秒
 * 2015-01-20 用文件锁代替信号量锁
 * 2014-01-13 告警短信治理
 * 2014-11-30 修复checkConfigMd5 notice, shm 全部加锁, \Config\PHPClient不存在fix, shm 11，12，13
 * 2014-11-28 屏蔽共享内存corrupted Warning 
 * 2014-11-27 支持客户端负载均衡，支持故障节点踢出及健康探测，支持权重设置
 * 2014-10-16 owl监控bugfix（$ctx="100";isset($ctx['error'])==true;）
 * 2014-09-12 修复kickAddress logger没初始化错误
 * 2014-07-15 去掉老的mnlogger埋点 加入tracelogger
 * 2014-07-08 支持owl_context上下文,支持日志追踪
 *
 *
 * 
 * 新 RPC 文本协议客户端实现
 *
 * @author XXXX
 *
 * @usage:
 *  1, 复制或软链接 RpcClient.php 到具体的项目目录中
 *  2, 添加 RpcServer 相关配置, 参考: examples/config/debug.php
 *  3, 在 Controller 中添加 RPC 使用代码, 参考下面的例子
 *
 * @example
 *
 *      $userInfo = RpcClient_User_Info::instance();
 *
 *      # case 1
 *      $result = $userInfo->getInfoByUid(100);
 *      if (!RpcClient::hasErrors($result)) {
 *          ...
 *      }
 *
 *      # case 2
 *      $userInfo->getInfoByUid(100, function ($result, $errors) {
 *          if (!$errors) {
 *              ...
 *          }
 *      });
 *
 *      # 其中 RpcClient_ 是接口调用约定
 *      # RpcClient_User_Info::getInfoByUid 映射到
 *      # WebService 中的 \User\Service\Info 类和 getInfoByUid 方法
 *
 * 用户认证算法
 *
 *      # 客户端
 *      $packet = array(
 *          'data' => json_encode(
 *              array(
 *                  'version' => '2.0',
 *                  'user' => $this->rpcUser,
 *                  'password' => md5($this->rpcUser . ':' . $this->rpcSecret),
 *                  'timestamp' => microtime(true)); # 时间戳用于生成不同的签名, 以区分每一个独立请求
 *                  'class' => $this->rpcClass,
 *                  'method' => $method,
 *                  'params' => $arguments,
 *              )
 *          ),
 *      );
 *      $packet['signature'] = $this->encrypt($packet['data'], $secret);
 *
 *      # 服务器端
 *      # $this->encrypt($rawJsonData, $secret) === $packet['signature']
 *
 * 获取网络数据
 *
 *      RpcClient::on('send', function ($data) { });
 *      RpcClient::on('recv', function ($data) { });
 */

/**
 * 客户端协议实现.
 */
class RpcClient
{
    /**
     * 存储RPC服务端节点共享内存的key
     * @var int
     */
    const BAD_ASSRESS_LIST_SHM_KEY = 0x90905743;
    
    /**
     * 当出现故障节点时，有多大的几率访问这个故障节点(默认万分之一)
     * @var float
     */
    const DETECTION_PROBABILITY = 0.0001;
    
    /**
     * 当出现故障节点时，有多大的几率访问这个故障节点(默认万分之一)
     * @var float
     */
    const ALARM_PROBABILITY = 0.1;
    
    /**
     * 避免雪崩，最多踢掉多少故障节点，按照百分比计算
     * @var float
     */
    const KICK_MAX_PERCENT = 0.67;
    
    /**
     * 保存上次告警时间的VAR
     * @var int
     */
    const RPC_LAST_ALARM_TIME_KEY = 1;
    
    /**
     * 故障节点共享内存fd
     * @var resource
     */
    protected static $badAddressShmFd = null;
    
    /**
     * 故障的节点列表
     * @var array
     */
    protected static $badAddressList = null;
    
    /**
     * 故障节点列表的共享内存的变量的key
     * @var int
     */
    protected static $badAddressListKey = null;
    
    /**
     * 信号量
     * @var resource
     */
    protected static $semFd = null;
    
    /**
     * 上次告警时间戳
     * @var int
     */
    protected static $lastAlarmTime = 0;
    
    /**
     * 告警时间间隔 单位:秒
     * @var int
     */
    protected static $alarmInterval = 300;
    
    /**
     * 排它锁文件handle
     * @var resource
     */
    protected static $lockFileHandle = null;
    
    /**
     * 阻塞锁
     * @var resource
     */
    protected static $lockHandle = null;
    
    /**
     * 是否可以踢掉故障节点
     * @var bool
     */
    protected $addressKickable = false;
    
    /**
     * 客户端链接超时设置 单位秒
     * @var int
     */
    public static $connectionTimeOut = 4;
    
    /**
     * 客户端接收超时设置 单位秒
     * @var int
     */
    public static $recvTimeOut = 18;
    
    /**
     * 短信告警（服务连不上）相关参数
     * @var int
     */
    public static $smsAlarmPrarm = array(
        // 接收告警的手机号，逗号(,)分割
        'phone'  => '',
        // 短信告警接口url
        'url'    => 'http://www.xxx.com/send',
        // 接口参数
        'params' => array(
                'channel' => 'monternet',
                'key' => 'notice_dnq11',
                'task' => 'int_ce23',
        ),
    );
    
    protected $connection;
    protected $rpcClass;
    protected $rpcUri;
    protected $rpcUser;
    protected $rpcSecret;
    protected $executionTimeStart;
    protected static $_config = array();

    protected static $events = array();

    /**
     * 设置或读取配置信息.
     *
     * @param array $config 配置信息.
     *
     * @return array|void
     */
    public static function config(array $config = array())
    {
        if (empty($config)) {
            return self::$_config;
        }
        self::$_config = $config;
    }

    /**
     * 获取RPC对象实例.
     *
     * @param array $config 配置信息, 或配置节点.
     *
     * @return RpcClient
     */
    public static function instance($config = array())
    {
        $className = get_called_class();

        static $instances = array();
        $key = $className . '-';
        if (empty($config)) {
            $key .= 'whatever';
        } else {
            $key .= md5(serialize($config));
        }
        if (empty($instances[$key]) || PHP_SAPI == 'cli') {
            $instances[$key] = new $className($config);
            $instances[$key]->rpcClass = $className;
        }
        
        return $instances[$key];
    }

    /**
     * 检查返回结果是否包含错误信息.
     *
     * @param mixed $ctx 调用RPC接口时返回的数据.
     *
     * @return boolean
     */
    public static function hasErrors(&$ctx)
    {
        if (is_array($ctx)) {
            if (isset($ctx['error'])) {
                $ctx = $ctx['error'];
                return true;
            }
            if (isset($ctx['errors'])) {
                $ctx = $ctx['errors'];
                return true;
            }
        }
        return false;
    }

    /**
     * 注册各种事件回调函数.
     *
     * @param string   $eventName     事件名称, 如: read, recv.
     * @param function $eventCallback 回调函数.
     *
     * @return void
     */
    public static function on($eventName, $eventCallback)
    {
        if (empty(self::$events[$eventName])) {
            self::$events[$eventName] = array();
        }
        array_push(self::$events[$eventName], $eventCallback);
    }

    /**
     * 调用事件回调函数.
     *
     * @param $eventName 事件名称.
     *
     * @return void.
     */
    protected static function emit($eventName)
    {
        if (!empty(self::$events[$eventName])) {
            $args = array_slice(func_get_args(), 1);
            foreach (self::$events[$eventName] as $callback) {
                @call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * 构造函数.
     *
     * @param array $config 配置信息, 或配置节点.
     *
     * @throws Exception 抛出开发错误信息.
     */
    protected function __construct(array $config = array())
    {
        if (empty($config)) {
            $config = self::config();
        } else {
            self::config($config);
        }

        $config = self::config();
        if(empty($config) && class_exists('\Config\PHPClient'))
        {
            $config = (array) new \Config\PHPClient;
            self::config($config);
        }

        if (empty($config)) {
            throw new Exception('RpcClient: Missing configurations');
        }
        
        self::$badAddressListKey = fileinode(__FILE__);
        if(!self::$badAddressListKey)
        {
        	self::$badAddressListKey = 500;
        }
        
        if(!empty($config['recv_time_out']) && (int)$config['recv_time_out'] > 0)
        {
        	self::$recvTimeOut = (int)$config['recv_time_out'];
        }
        
    	if(!empty($config['connection_time_out']) && (int)$config['connection_time_out'] > 0)
        {
        	self::$connectionTimeOut = (int)$config['connection_time_out'];
        }

        $className = get_called_class();
        if (preg_match('/^[A-Za-z0-9]+_([A-Za-z0-9]+)/', $className, $matches)) {
            $module = $matches[1];
            if (empty($config[$module])) {
                throw new Exception(sprintf('RpcClient: Missing configuration for `%s`', $module));
            } else {
                $this->init($config[$module]);
            }
        } else {
            throw new Exception(sprintf('RpcClient: Invalid class name `%s`', $className));
        }

        // $this->openConnection();
    }

    /**
     * 析构函数.
     */
    public function __destruct()
    {
        // $this->closeConnection();
    }

    /**
     * 读取初始化配置信息.
     *
     * @param array $config 配置.
     *
     * @return void
     */
    public function init(array $config)
    {
        $config = $this->filter($config);
        $this->rpcUri = $config['uri'];
        $this->rpcUser = $config['user'];
        $this->rpcSecret = $config['secret'];
        $this->rpcCompressor = isset($config['compressor']) ? strtoupper($config['compressor']) : null;
    }
    
    /**
     * 过滤掉故障uri
     * @param array $config
     * @throws \Exception
     */
    protected function filter($config)
    {
        $this->addressKickable = false;
        // uri配置错误
        if(!is_string($config['uri']) && !is_array($config['uri']))
        {
            throw new \Exception("config error. " . var_export($config, true));
        }
        // tcp://127.0.0.1:9091 or 127.0.0.1:9191
        if(is_string($config['uri']))
        {
            $config['uri'] = $this->formatUri($config['uri']);
            return $config;
        }
        // 没有安装sysvshm，无法过滤故障节点，则随机挑选一个节点使用
        if(!extension_loaded('sysvshm') || (defined('\Config\PHPClient::HEALTH_DETECTION') && !\Config\PHPClient::HEALTH_DETECTION))
        {
            $config['uri'] = $this->formatUri($config['uri'][array_rand($config['uri'])]);
            return $config;
        }
        
        $address_weight_map = $this->uriToAddress($config['uri']);
        // 获取一个可用address
        $address = $this->getOneAddress($address_weight_map);
        $config['uri'] = $this->formatUri($address);
        return $config;
    }
    
    /**
     * [tcp://ip:port:24,tcp://ip:port:32..] 转化为 [ip:port=>24,ip:port=>32]格式
     */
    protected function uriToAddress($uris)
    {
        $address_weight_map = array();
        if(is_array($uris))
        {
            foreach($uris as $uri)
            {
                $address = $weight = null;
                $this->formatAddress($uri, $address, $weight);
                $address_weight_map[$address] = $weight;
            }
        }
        return $address_weight_map;
    }
    
    /**
     * 格式化address
     */
    protected function formatAddress($uri, &$address, &$weight)
    {
        // tcp://192.168.1.1:9091:24
        $tmp = str_replace('tcp://', '', $uri);
        $tmp = explode(':', $tmp);
        $address = "$tmp[0]:$tmp[1]";
        if(isset($tmp[2]))
        {
            $weight = $tmp[2];
        }
        else
        {
            $weight = 1;
        }
    }
    
    /**
     * ip:prort:weight 转化为 tcp://ip:port
     * @param unknown_type $address
     */
    protected function formatUri($uri)
    {
        $address = $weight = '';
        $this->formatAddress($uri, $address, $weight);
        return 'tcp://'.$address;
    }

    /**
     * 创建网络链接.
     *
     * @throws Exception 抛出链接错误信息.
     *
     * @return void
     */
    protected function openConnection()
    {
    	$this->executionTimeStart = microtime(true);
        $this->connection = @stream_socket_client($this->rpcUri, $errno, $errstr, self::$connectionTimeOut);
        if (!$this->connection) {
            /*if(class_exists("\\Thrift\\Client", false) && is_object(\Thrift\Client::$logger))
            {
                $address = substr($this->rpcUri, 6);
                \Thrift\Client::kickAddress($address);
            }
            else*/
            {
                $address = substr($this->rpcUri, 6);
                $this->kickAddress($address);
            }
            // 有一定几率触发告警
            $percent = defined('\Config\PHPClient::ALARM_PROBABILITY') ? \Config\PHPClient::ALARM_PROBABILITY : self::ALARM_PROBABILITY;
            if(rand(1, 10000)/10000 <= $percent)
            {
            	$local_ip = self::getLocalIp();
            	$bad_address_list = self::getBadAddressList();
            	$alarm_data = array(
                    'type' => 7,
                    'ip' => $local_ip,
                    'target_ip' => array($address),
            	);
            	self::sendSmsAlarm($alarm_data, '告警消息 PHPServer客户端监控 客户端 '.$local_ip." 链接 $address 失败 时间：".date('Y-m-d H:i:s'));
            }
            throw new Exception(sprintf('RpcClient: %s, %s (%.3fs)', $this->rpcUri, $errstr, $this->executionTime()));
        }
        @stream_set_timeout($this->connection, self::$recvTimeOut);
    }

    /**
     * 关闭网络链接.
     *
     * @return void
     */
    protected function closeConnection()
    {
        @fclose($this->connection);
    }

    /**
     * 请求数据签名.
     *
     * @param string $data   待签名的数据.
     * @param string $secret 私钥.
     *
     * @return string
     */
    protected function encrypt($data, $secret)
    {
        return md5($data . '&' . $secret);
    }

    /**
     * 调用 RPC 方法.
     *
     * @param string $method    PRC 方法名称.
     * @param mixed  $arguments 方法参数.
     *
     * @throws Exception 抛出开发用的错误提示信息.
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $sign = '' . $this->rpcSecret;

        $packet = array(
            'data' => json_encode(
                array(
                    'version' => '2.0',
                    'user' => $this->rpcUser,
                    'password' => md5($this->rpcUser . ':' . $this->rpcSecret),
                    'timestamp' => microtime(true),
                    'class' => $this->rpcClass,
                    'method' => $method,
                    'params' => $arguments,
                )
            ),
        );

        $config = self::config();
        
        $logdir = isset($config['trace_log_path']) ? $config['trace_log_path'] : '/home/logs/monitor';
        
        $trace_config = array(
           'on' => true,
           'app' => defined('APP_NAME') ? APP_NAME : 'php-rpc-client',
           'logdir' => $logdir,
        );
        
        $trace_logger = null;
        
        try{
            $trace_logger = @\MNLogger\TraceLogger::instance($trace_config);
        }
        catch(\Exception $e){}
        
        
        $trace_logger && $trace_logger->RPC_CS(substr($this->rpcUri, 6), $this->rpcClass, $method, $arguments);

        $packet['signature'] = $this->encrypt($packet['data'], $config['rpc_secret_key']);

        try{
        	// uri 参数其实不需要，这里方便调用超时打印出调用栈，看到uri参数
            $ctx = $this->remoteCall($packet, substr($this->rpcUri, 6));
        }
        catch (\Exception $e)
        {
            $trace_logger && $trace_logger->RPC_CR('EXCEPTION', strlen($e), $e);
            throw $e;
        }

        if (is_array($ctx) && isset($ctx['exception']) && is_array($ctx['exception'])) {
            $trace_logger && $trace_logger->RPC_CR('EXCEPTION', strlen(json_encode($ctx)), $ctx);
            throw new Exception('RPC Exception: '.$this->rpcClass.'::'.$method.' URI：' . $this->rpcUri . ' return ' . var_export($ctx['exception'], true));
        }
        elseif(is_array($ctx) && isset($ctx['error']) || is_array($ctx) && isset($ctx['errors']))
        {
            $trace_logger && $trace_logger->RPC_CR('EXCEPTION', strlen(json_encode($ctx)), $ctx);
        }
        else 
        {
            $trace_logger && $trace_logger->RPC_CR('SUCCESS', strlen(json_encode($ctx)));
        }

        return $ctx;
    }

    /**
     * 发起 RPC 调用协议.
     *
     * @param array $data RPC 数据.
     *
     * @throws Exception 抛出开发用的错误提示信息.
     *
     * @return mixed
     */
    protected function remoteCall(array $data)
    {
        $this->executionTimeStart = microtime(true);

        // owl trace
        global $owl_context, $context;
        $context = !is_array($context) ? array() : $context;
        $owl_context_client = $owl_context;
        if(!empty($owl_context_client))
        {
            $owl_context_client['app_name'] = defined('JM_APP_NAME') ? JM_APP_NAME : 'undefined';
        }
        $context['owl_context'] = json_encode($owl_context_client);
        $data['CONTEXT'] = $context;

        $this->openConnection();

        // 用 JSON 序列化请求数据
        if (!$data = json_encode($data)) {
            throw new Exception('RpcClient: Cannot serilize $data with json_encode');
        }

        $fp = $this->connection;

        // 压缩数据
        $command = 'RPC';

        // 发送 RPC 文本请求协议
        $buffer = sprintf("%d\n%s\n%d\n%s\n", strlen($command), $command, strlen($data), $data);
        if (!@fwrite($fp, $buffer)) {
            throw new Exception(sprintf('RpcClient: Network %s disconnected', $this->rpcUri));
        }
        self::emit('send', $data);

        // 读取 RPC 返回数据的长度信息
        if (!$length = @fgets($fp)) {
        	$execution_time = $this->executionTime();
        	if($execution_time>=self::$recvTimeOut)
        	{
                throw new Exception(
                	sprintf(
                        'RpcClient: Network %s recv timeout (%.3fs)',
                        $this->rpcUri,
                        $execution_time
                	)
                );
        	}
            throw new Exception(
                sprintf(
                    'RpcClient: Network %s connection closed (%.3fs)',
                    $this->rpcUri,
                    $execution_time
                )
            );
        }
        $length = trim($length);
        if (!preg_match('/^\d+$/', $length)) {
            throw new Exception(sprintf('RpcClient: Got wrong protocol codes: %s', bin2hex($length)));
        }
        $length = 1 + $length; // 1 means \n

        // 读取 RPC 返回的具体数据
        $ctx = '';
        while (strlen($ctx) < $length) {
            $ctx .= fgets($fp);
        }
        self::emit('recv', $ctx);
        $ctx = trim($ctx);

        $this->closeConnection();

        // 反序列化 JSON 数据并返回
        if ($ctx !== '') {
            if ($this->rpcCompressor === 'GZ') {
                $ctx = @gzuncompress($ctx);
            }
            $ctx = json_decode($ctx, true);
            return $ctx;
        }
    }

    /**
     * 计算 RPC 请求时间.
     *
     * @return float
     */
    protected function executionTime()
    {
        return microtime(true) - $this->executionTimeStart;
    }
    
    /**
     * 获取故障节点列表
     * @return array
     */
    public static function getBadAddressList($use_cache = true)
    {
        // 没有加载扩展
        if(!extension_loaded('sysvshm'))
        {
            self::$badAddressList = array();
            return array();
        }
    
        // 还没有初始化故障节点
        if(null === self::$badAddressList || !$use_cache)
        {
            // 是否有故障节点
            self::getMutex();
            $ret = shm_has_var(self::getShmFd(), self::$badAddressListKey);
            self::releaseMutex();
            if(!$ret || rand(1, 10000000) == 1)
            {
            	self::$badAddressList = array();
            	if(rand(1, 1000) == 1)
            	{
            	    self::getMutex();
            	    // 尝试删除共享内存
            	    $ret2 = self::removeShm();
            	    self::releaseMutex();
            	    self::log("getBadAddressList try to remove shm ret:".var_export($ret, true)." " . var_export($ret2, true));
            	}
            }
            else
            {
                // 获取故障节点
            	self::getMutex();
                $bad_address_list = @shm_get_var(self::getShmFd(), self::$badAddressListKey);
                self::releaseMutex();
                if(!is_array($bad_address_list))
                {
                	self::getMutex();
                    // 出现错误，可能是共享内存写坏了，删除共享内存
                    $ret = self::removeShm();
                    self::releaseMutex();
                    self::log("getBadAddressList fail bad_address_list:".var_export($bad_address_list,true) . ' ret:'.var_export($ret, true));
                    self::$badAddressList = array();
                }
                else
                {
                    self::$badAddressList = $bad_address_list;
                }
            }
        }
        return self::$badAddressList;
    }
    
    /**
     * 获取写锁(睡眠锁)
     * @return true
     */
    public static function getMutex()
    {
        //self::getSemFd() && sem_acquire(self::getSemFd());
        if(!self::$lockHandle)
        {
        	self::$lockHandle = fopen(__FILE__, 'r');
        }
        if(self::$lockHandle)
        {
        	$try_count = 3;
        	for($i=0; $i<$try_count; $i++)
        	{
                if(flock(self::$lockHandle, LOCK_EX | LOCK_NB))
                {
                	return true;
                }
        	}
        }
        return false;
    }
    
    /**
     * 释放写锁（睡眠锁）
     * @return true
     */
    public static function releaseMutex()
    {
        //self::getSemFd() && sem_release(self::getSemFd());
    	if(!self::$lockHandle)
    	{
            return;
    	}
    	return flock(self::$lockHandle, LOCK_UN);
    }
    
    /**
     * 获取排它锁
     */
    public static function getLock()
    {
        self::$lockFileHandle = @fopen("/tmp/RPC_CLIENT_SEND_SMS_ALARM.lock", "w");
        @chmod("/tmp/RPC_CLIENT_SEND_SMS_ALARM.lock", 0777);
        return self::$lockFileHandle && flock(self::$lockFileHandle, LOCK_EX | LOCK_NB);
    }
    
    /**
     * 释放排它锁
     */
    public static function releaseLock()
    {
        return self::$lockFileHandle && flock(self::$lockFileHandle, LOCK_UN);
    }
    
    protected static function removeShm()
    {
    	@shm_remove(self::$badAddressShmFd);
    	self::$badAddressShmFd = null;
    	$ret = @shm_put_var(self::getShmFd(), self::$badAddressListKey, array());
    	return $ret;
    }
    
    /**
     * 获得信号量fd(停用)
     * @return null/resource
     */
    public static function getSemFd()
    {
        if(!self::$semFd && extension_loaded('sysvsem'))
        {
            self::$semFd = sem_get(self::BAD_ASSRESS_LIST_SHM_KEY);
        }
        return self::$semFd;
    }
    
    /**
     * 获取故障节点共享内存的Fd（停用）
     * @return resource
     */
    public static function getShmFd()
    {
        if(!extension_loaded('sysvshm'))
        {
            return false;
        }
        if(!self::$badAddressShmFd)
        {
            self::$badAddressShmFd = @shm_attach(self::BAD_ASSRESS_LIST_SHM_KEY, 1024000);
        }
        return self::$badAddressShmFd;
    }
    
    /**
     * 获取一个可用节点
     * @param array $address_list  address和权重映射关系 ['10.0.1.2:9091'=>24, '10.0.1.3:9091'=>24, '10.0.1.4:9091'=>32, ...]
     * @throws \Exception
     * @return string
     */
    public function getOneAddress($address_weight_map)
    {
        // 权重相关
        $address_weight_map_copy = $address_weight_map;
        foreach($address_weight_map as $address=>$weight)
        {
            // 检查权重
            if((string)intval($weight) !== (string)$weight || $weight < 0)
            {
                throw new \Exception("$address:$weight . Weight mast be integer and not negative");
            }
            // 权重为0的去除
            if((int)$weight === 0)
            {
                unset($address_weight_map[$address]);
                continue;
            }
        }
        if(empty($address_weight_map))
        {
            $err_msg = 'no available addresses '. json_encode($address_weight_map_copy);
            self::log($err_msg);
            throw new \Exception($err_msg);
        }
        $address_list = array_keys($address_weight_map);
        
        // 获取故障节点列表
        $bad_address_list = self::getBadAddressList(false);
        if($bad_address_list)
        {
            // 获得属于本次服务的故障ip列表
            $bad_address_list = array_intersect($bad_address_list, $address_list);
        }
        
        // 故障节点数占比大于self::KICK_MAX_PERCENT时不能再踢出ip（防止雪崩）
        $kick_max_percent = defined('\Config\PHPClient::KICK_MAX_PERCENT') ? \Config\PHPClient::KICK_MAX_PERCENT : self::KICK_MAX_PERCENT;
        if((count($bad_address_list)+1)/(count($address_list)) <= $kick_max_percent)
        {
            // 可以踢掉ip
            $this->addressKickable = true;
        }
    
        // 从节点列表中去掉故障节点列表
        if($bad_address_list)
        {
            $address_list = array_diff($address_list, $bad_address_list);
            $detection_probability = defined('\Config\PHPClient::DETECTION_PROBABILITY') ? \Config\PHPClient::DETECTION_PROBABILITY : self::DETECTION_PROBABILITY;
            // 一定的几率访问故障节点，用来探测故障节点是否已经存活
            if(empty($address_list) || rand(1, 1000000)/1000000 <= $detection_probability)
            {
                $one_bad_address = $bad_address_list[array_rand($bad_address_list)];
                self::recoverAddress($one_bad_address);
                $this->addressKickable = true;
                return $one_bad_address;
            }
            // $address_weight_map去掉故障ip
            $tmp = $address_weight_map;
            $address_weight_map = array();
            foreach($address_list as $address)
            {
                $address_weight_map[$address] = $tmp[$address];
            }
        }
        
        // 如果没有可用的节点,尝试使用一个故障节点
        if (empty($address_list))
        {
            // 连故障节点都没有？
            if(empty($bad_address_list))
            {
                $e =  new \Exception("No avaliable server node! " . json_decode($address_weight_map_copy));
                self::log($e);
                throw $e;
            }
            $address = $bad_address_list[array_rand($bad_address_list)];
            self::recoverAddress($address);
            $e =  new \Exception("No avaliable server node! Try to use a bad address:$address allAddress：". json_decode($address_weight_map_copy) ." badAddress:[" . implode(',', $bad_address_list).']');
            self::log($e);
            return $address;
        }
    
        // 总权重
        $weight_value = 0;
        foreach($address_weight_map as $address=>$weight)
        {
            $weight_value += $weight;
        }
        if($weight_value < 1)
        {
            throw new \Exception("no available address. all weight is zero");
        }
        // 带权重随机选择一个节点
        $rand_value = rand(1, $weight_value);
        $current_weight = 0;
        foreach($address_weight_map as $address=>$weight)
        {
            $current_weight += $weight;
            if($current_weight >= $rand_value)
            {
                return $address;
            }
        }
        self::log("can not find address width weight. rand_value:$rand_value current_weight:$current_weight address_weight_map:".json_encode($address_weight_map));
        return $address_list[array_rand($address_list)];
    }
    
    /**
     * 发送短信告警
     * @param string $content
     */
    public static function sendSmsAlarm($alarm_data, $content)
    {
        // 另外有进程已经在发告警短信了
        if(!self::getLock())
        {
            return true;
        }
        // 上次告警时间
        $last_alarm_time = self::getLastAlarmTime();
        if(!$last_alarm_time)
        {
            self::releaseLock();
            return false;
        }
        $time_now = time();
        // 时间间隔小于5分钟则不告警
        if($time_now - $last_alarm_time < self::$alarmInterval)
        {
            self::releaseLock();
            return;
        }
        // 短信告警
        if(empty(self::$smsAlarmPrarm['phone']) && class_exists('\Config\PHPClient') && isset(\Config\PHPClient::$smsAlarmPrarm))
        {
            self::$smsAlarmPrarm = \Config\PHPClient::$smsAlarmPrarm;
        }
        
        $url = self::$smsAlarmPrarm['url'];
        $phone_array = self::$smsAlarmPrarm['phone'] ? explode(',', self::$smsAlarmPrarm['phone']) : array();
        $params = self::$smsAlarmPrarm['params'];
        if($phone_array)
        {
            foreach($phone_array as $phone)
            {
                $alarm_data['phone'] = $phone;
                if(!self::sendAlarm($alarm_data))
                {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array_merge(array('num'=>$phone,'content'=>$content) , $params)));
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    self::log('send msg ' . $phone . ' ' . $content. ' send_ret:' .var_export(curl_exec($ch), true));
                }
            }
        }
        else
        {
            self::log('send msg but phone not set. ' . $content);
        }
        self::setLastAlarmTime($time_now);
        return self::releaseLock();
    }
    
    protected static function sendAlarm($data)
    {
        $alarm_uri = isset(self::$smsAlarmPrarm['alarm_uri']) ? self::$smsAlarmPrarm['alarm_uri'] : '';
        if(!$alarm_uri)
        {
            $alarm_uri = 'tcp://10.1.27.12:2015';
        }
        $client = stream_socket_client($alarm_uri, $err_no, $err_msg, 1);
        if(!$client)
        {
            self::log("sendAlarm fail . $err_msg");
            return false;
        }
        stream_set_timeout($client, 1);
        $buffer = json_encode($data);
        $send_len = fwrite($client, $buffer);
        if($send_len !== strlen($buffer))
        {
            self::log("sendAlarm fail . fwrite return " . var_export($send_len, true));
            return false;
        }
        //fread($client, 8196);
        self::log($buffer);
        return true;
    }
    
    /**
     * 获取上次告警时间
     */
    public static function getLastAlarmTime()
    {
        // 没有加载扩展
        if(!extension_loaded('sysvshm'))
        {
            return false;
        }
        // 是否有保存上次告警时间
        self::getMutex();
        $ret = shm_has_var(self::getShmFd(), self::RPC_LAST_ALARM_TIME_KEY);
        self::releaseMutex();
        if(!$ret)
        {
            $time_now = time();
            self::setLastAlarmTime($time_now);
            return $time_now-self::$alarmInterval;
        }
        
        self::getMutex();
        $ret = shm_get_var(self::getShmFd(), self::RPC_LAST_ALARM_TIME_KEY);
        self::releaseMutex();
        return $ret;
    }
    
    /**
     * 设置上次告警时间
     * @param int $timestamp
     */
    public static function setLastAlarmTime($timestamp)
    {
        // 没有加载扩展
        if(!extension_loaded('sysvshm'))
        {
            return false;
        }
        self::getMutex();
        $ret = shm_put_var(self::getShmFd(), self::RPC_LAST_ALARM_TIME_KEY, $timestamp);
        self::releaseMutex();
        return $ret;
    }
    
    /**
     * 恢复一个节点
     * @param string $address
     * @bool
     */
    public static function recoverAddress($address)
    {
        if(!extension_loaded('sysvshm'))
        {
            return false;
        }
        $bad_address_list = self::getBadAddressList(false);
        if(empty($bad_address_list) || !in_array($address, $bad_address_list))
        {
            return true;
        }
        $bad_address_list_flip = array_flip($bad_address_list);
        unset($bad_address_list_flip[$address]);
        $bad_address_list = array_keys($bad_address_list_flip);
        self::$badAddressList = $bad_address_list;
        self::getMutex();
        $ret = shm_put_var(self::getShmFd(), self::$badAddressListKey, $bad_address_list);
        self::releaseMutex();
        self::log("recoverAddress $address now bad_address_list:[".implode(',', $bad_address_list).'] shm write ret:'.var_export($ret, true));
        return $ret;
    }
    
    
    public function kickAddress($address)
    {
        // 没有加载扩展
        if(!extension_loaded('sysvshm'))
        {
            return false;
        }
        
        $bad_address_list = self::getBadAddressList(false);
        
        // 是否可踢出
        if(!$this->addressKickable)
        {
            self::log("kickAddress $address but addressKickable is false now bad address " . json_encode($bad_address_list));
            return false;
        }
        
        $bad_address_list[] = $address;
        $bad_address_list = array_unique($bad_address_list);
        self::$badAddressList = $bad_address_list;
        self::getMutex();
        $ret = shm_put_var(self::getShmFd(), self::$badAddressListKey, $bad_address_list);
        self::releaseMutex();
        self::log("kickAddress($address) now bad_address_list:[".implode(',', $bad_address_list).'] shm write ret:'.var_export($ret, true));
        return $ret;
    }
    
    protected static function log($msg)
    {
        if(defined('\Config\PHPClient::LOG_DIR'))
        {
            $log_file = \Config\PHPClient::LOG_DIR . '/phpclient.log';
        }
        else
        {
            $log_file = '/tmp/phpclient.log';
        }
        @file_put_contents($log_file, date('Y-m-d H:i:s')." ".$msg."\n", FILE_APPEND);
    }
    
    /**
     * 获得本机ip
     */
    public static function getLocalIp()
    {
        if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != '127.0.0.1')
        {
            $ip = $_SERVER['SERVER_ADDR'];
        }
        else
        {
            $ip = gethostbyname(trim(`hostname`));
        }
        return $ip;
    }

}

spl_autoload_register(
    function ($className) {
        if (strpos($className, 'RpcClient_') !== 0)
            return false;

        eval(sprintf('class %s extends \PHPClient\RpcClient {}', $className));
    }, true, true
);

if (false) {
    $config = array(
        'rpc_secret_key' => '769af463a39f077a0340a189e9c1ec28',
        'monitor_log_path'  => '/home/logs/monitor',
        'trace_log_path'    => '/home/logs/monitor',
        'exception_log_path'=> '/home/logs/monitor',
        'User' => array(
            'uri' => 'tcp://127.0.0.1:2201',
            'user' => 'Optool',
            'secret' => '{1BA09530-F9E6-478D-9965-7EB31A59537E}',
            //'compressor' => 'GZ',
        ),
        'Item' => array(
            'uri' => 'tcp://127.0.0.1:2201',
            'user' => 'Optool',
            'secret' => '{1BA09530-F9E6-478D-9965-7EB31A59537E}',
        ),
        'Order' => array(
            'uri' => 'tcp://127.0.0.1:2201',
            'user' => 'Optool',
            'secret' => '{1BA09530-F9E6-478D-9965-7EB31A59537E}',
        ),
    );

    \PHPClient\RpcClient::on('send', function($data) {
        echo 'Send => ', $data, PHP_EOL;
    });
    \PHPClient\RpcClient::on('recv', function($data) {
        echo 'Recv <= ', $data, PHP_EOL;
    });

    \PHPClient\RpcClient::config($config);
    //$test = RpcClient_Item_Iwc::instance();
    //var_export($test->getInventoryByWarehouses(array(100223,100002,100003,100006), array('BJ08','GZ07','SH05')));

    $test = \RpcClient_User_Address::instance();
    //var_dump($test->getListByUid(5100));
    $test->getListByUid(5100, function () {
        var_dump(func_get_args());
    });
}
