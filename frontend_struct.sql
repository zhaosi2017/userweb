DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(64) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `nickname` varchar(64) DEFAULT NULL,
  `auth_key` varchar(64) DEFAULT NULL,
  `un_call_number` int(10) unsigned NOT NULL DEFAULT '0',
  `un_call_by_same_number` int(10) unsigned NOT NULL DEFAULT '0',
  `time_range` int(10) unsigned NOT NULL DEFAULT '0',
  `country_code` int(10) unsigned DEFAULT NULL,
  `phone_number` varchar(64) NOT NULL DEFAULT '',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `reg_ip` varchar(64) NOT NULL DEFAULT '',
  `whitelist_switch` tinyint(1) NOT NULL DEFAULT '0',
  `language` VARCHAR(40) NOT NULL DEFAULT 'zh-CN',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `step` TINYINT(1) NOT NULL DEFAULT '0',
  `token` VARCHAR(255)  DEFAULT NULL  COMMENT '令牌',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_phone`;
CREATE TABLE `user_phone` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `phone_country_code` char(8) NOT NULL DEFAULT '+86' COMMENT '电话号码的国际编码',
  `user_phone_number` char(16) NOT NULL DEFAULT '' COMMENT '电话号码',
  `reg_time` int(11) NOT NULL DEFAULT '0' COMMENT '绑定时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `user_phone_sort` int(11) NOT NULL DEFAULT '1' COMMENT '号码在用户下的顺序, 数字小优先级高',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_urgent_contact`;
CREATE TABLE user_urgent_contact (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  contact_country_code char(32) NOT NULL DEFAULT '86' COMMENT '国际编码',
  contact_phone_number char(32) NOT NULL DEFAULT '0' COMMENT '电话号码',
  contact_nickname char(64) NOT NULL DEFAULT '' COMMENT '联系人昵称',
  reg_time int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  update_time int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  contact_sort int(11) NOT NULL DEFAULT '1' COMMENT '紧急联系人的优先顺序, 数字小优先级高',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `white_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `white_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `white_uid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `black_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `black_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `black_uid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `channel`;
CREATE TABLE `channel` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT '渠道名称',
  `img_url` VARCHAR(255) NOT NULL COMMENT '渠道对应图片的URL地址',
  `sort` tinyint(4) NOT NULL COMMENT '排序',
  `create_at` int(11) NOT NULL DEFAULT '0',
  `gray_img_url` varchar(150) NOT NULL COMMENT '灰色图片',
  `update_at` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '题目',
  `create_at` int(11) NOT NULL,
  `update_at` int(11) NOT NULL,
  `type` smallint(3) NOT NULL COMMENT '题组：1:1组，2：2组，3:3组',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `security_question`;
CREATE TABLE `security_question` (
  `id` mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '用户id',
  `q_one` smallint(5) NOT NULL COMMENT '第一个密保问题id(对应question表对应的id)',
  `a_one` varchar(255) NOT NULL COMMENT '第一个密保的答案',
  `q_two` smallint(5) NOT NULL COMMENT '第二个密保问题id',
  `a_two` varchar(255) NOT NULL COMMENT '第二个密保的答案',
  `q_three` smallint(5) NOT NULL COMMENT '第三个密保问题id',
  `a_three` varchar(255) NOT NULL COMMENT '第三个密保的答案',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*!user表添加渠道 */;
ALTER TABLE `user` ADD `channel` VARCHAR(255) NULL DEFAULT NULL COMMENT '渠道' AFTER `token`;

ALTER TABLE `user` ADD `balance` DECIMAL(14,4) NOT NULL DEFAULT '0.0000'  COMMENT '余额' AFTER `channel`;
ALTER TABLE `user` ADD `header_img` VARCHAR (255)   DEFAULT NULL  COMMENT '头像' AFTER `balance`;

/********************通话记录表********************/
CREATE TABLE `call_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` int(11) DEFAULT NULL COMMENT '主叫id',
  `call_id` char(64) DEFAULT NULL COMMENT '呼叫的id',
  `to_user_id` int(11) DEFAULT NULL COMMENT '被叫id',
  `time` int(11) DEFAULT NULL COMMENT '记录时间',
  `text` char(255) DEFAULT '' COMMENT '语音类容',
  `duration` int(4) DEFAULT NULL COMMENT '通话时间',
  `amount` decimal(14,4) DEFAULT NULL COMMENT '通话金额',
  `status` int(11) DEFAULT NULL COMMENT '通话状态',
  `call_type` int(11) DEFAULT NULL COMMENT '呼叫类型',
  `from_number` char(32) DEFAULT NULL COMMENT '主叫电话',
  `to_number` char(32) DEFAULT NULL COMMENT '被叫电话',
  `third` char(255) DEFAULT NULL COMMENT '呼叫渠道',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/********************好友列表***************************/
CREATE TABLE `friends` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `friend_id` int(11) NOT NULL COMMENT '好友id',
  `create_at` int(11) NOT NULL COMMENT '创建时间',
  `remark` char(64) DEFAULT '' COMMENT '备注',
  `extsion` text COMMENT '扩展信息json',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户组id 0表示为分组',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `friends_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `group_name` char(128) NOT NULL DEFAULT '' COMMENT '组名',
  `create_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/********************好友请求表***************************/
CREATE TABLE `friends_request` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL COMMENT '发送请求者',
  `to_id` int(11) NOT NULL COMMENT '被请求者',
  `note` varchar(255) NOT NULL  COMMENT '请求备注',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0:发送请求，1:同意，2:拒绝',
  `create_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `friends` ADD `direction` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '请求方向：0主动发送请求，1接受好友请求' AFTER `extsion`;
ALTER TABLE `friends` ADD `link_time` int(11) unsigned DEFAULT '0' COMMENT '最近联系时间' AFTER `group_id`;
ALTER TABLE `friends_request` ADD `update_at` int(11)  DEFAULT '0' COMMENT '更新时间' AFTER `create_at`;
ALTER TABLE `call_record` ADD `group_id` char(64)  DEFAULT NULL COMMENT '呼叫组记录id' AFTER `third`;




/********************好友请求表***************************/
CREATE TABLE `user_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '登录用户id',
  `address` varchar(255) DEFAULT NULL COMMENT '登录地址',
  `longitude` varchar(64) DEFAULT NULL COMMENT '经度',
  `latitude` varchar(64) DEFAULT NULL COMMENT '纬度',
  `login_ip` varchar(64) DEFAULT NULL COMMENT '登录IP',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `user` ADD `address` VARCHAR (255)   DEFAULT NULL  COMMENT '注册地址' AFTER `balance`;
ALTER TABLE `user` ADD `longitude` VARCHAR (64)   DEFAULT NULL  COMMENT '经度' AFTER `address`;
ALTER TABLE `user` ADD `latitude` VARCHAR (64)   DEFAULT NULL  COMMENT '纬度' AFTER `longitude`;

CREATE TABLE `tts_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '日志类型',
  `url` char(255) DEFAULT '' COMMENT '交互的ip／url',
  `data` text COMMENT '交互的内容',
  `object` text COMMENT '交互时的对象状态',
  `time` date DEFAULT NULL COMMENT '时间',
  `number` char(255) DEFAULT NULL COMMENT '电话号码',
  `call_id` char(255) DEFAULT NULL COMMENT '呼叫id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/********************版本控制***************************/
CREATE TABLE `version` (
  `id` mediumint(9) unsigned  NOT NULL AUTO_INCREMENT,
  `platform` varchar(64) NOT NULL COMMENT '平台:android,ios',
  `version` varchar(64) NOT NULL COMMENT '版本号',
  `info` text NOT NULL COMMENT '版本更新内容',
  `url` varchar(255) NOT NULL COMMENT '版本的地址',
  `create_at` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `friends` ADD `is_new_friend` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否是新朋友：0是，1否' AFTER `direction`;


/************************邮箱字段（目前暂时不加，只是做的预留，以后如果加邮箱注册等功能在添加）*******************************／
ALTER TABLE `user` ADD `email` VARCHAR(64) NULL DEFAULT NULL COMMENT '邮箱' AFTER `account`;
************************************************************************************************/






CREATE TABLE `manager` (
  `id` int(10) UNSIGNED NOT NULL,
  `auth_key` varchar(64) DEFAULT NULL,
  `password` varchar(64) NOT NULL DEFAULT '',
  `account` text,
  `nickname` text,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `remark` text,
  `login_ip` varchar(64) NOT NULL DEFAULT '',
  `create_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_at` int(11) DEFAULT '0',
  `update_at` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `manager`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;