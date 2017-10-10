
/******第一步****先导库******/
/*****第二步在执行该sql*****/

ALTER TABLE `user` CHANGE `account` `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


/**呼叫记录***/
ALTER TABLE `call_record` ADD `call_id` CHAR(64) NULL DEFAULT NULL AFTER `active_nickname`, ADD `text` CHAR(255) NULL DEFAULT NULL AFTER `call_id`, ADD `duration` INT(4) NOT NULL DEFAULT '0' AFTER `text`, ADD `amount` DECIMAL(14,4) NULL DEFAULT NULL AFTER `duration`, ADD `group_id` CHAR(64) NULL DEFAULT NULL AFTER `amount`, ADD `third` CHAR(255) NULL DEFAULT NULL AFTER `group_id`;



/***user表***/
ALTER TABLE `user` ADD `account` VARCHAR(255) NULL DEFAULT NULL COMMENT '邮箱' AFTER `email`, ADD `token` VARCHAR(255) NULL DEFAULT NULL COMMENT '令牌' AFTER `email`, ADD `channel` VARCHAR(255) NULL DEFAULT NULL COMMENT '渠道' AFTER `token`, ADD `address` VARCHAR(255) NULL DEFAULT NULL COMMENT '注册地址' AFTER `channel`, ADD `longitude` VARCHAR(64) NULL DEFAULT NULL COMMENT '注册经度' AFTER `address`, ADD `latitude` VARCHAR(64) NULL DEFAULT NULL COMMENT '注册纬度' AFTER `longitude`, ADD `header_img` VARCHAR(255) NULL DEFAULT NULL COMMENT '头像地址' AFTER `latitude`, ADD `balance` DECIMAL(14,4) NULL DEFAULT '0.0000' COMMENT '余额' AFTER `header_img`;


ALTER TABLE `user` ADD UNIQUE(`account`);
/***
** call_record     修改结构
** user            修改结构
** tmp_report_call
** channel
** customer
** friends
** friends_group
** friends_request
** message_catch
** question
** security_question
** tmp_report_call
** tts_log
** user_login_log
** version
 */


/***#统计好友电话**/



/*****渠道*****/

CREATE TABLE `channel` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '渠道名称',
  `img_url` varchar(255) NOT NULL COMMENT '渠道对应图片的URL地址',
  `gray_img_url` varchar(200) NOT NULL COMMENT '灰色图片',
  `sort` tinyint(4) NOT NULL COMMENT '排序',
  `create_at` int(11) NOT NULL DEFAULT '0',
  `update_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



INSERT INTO `channel` (`id`, `name`, `img_url`, `gray_img_url`, `sort`, `create_at`, `update_at`) VALUES
(1, 'potato', '/20170929/channel_023b3166623faaf32f88feae2f579204.png', '/20170927/channel_eacf53172b3553a41c4b60601e9e2322.png', 1, 1504105006, 1506674753),
(2, 'telegram', '/20170929/channel_4791f8585ddedb7b016815c4283f9568.png', '/20170927/channel_7452e80fd99b61d53cd9eb3596eac2eb.png', 0, 1506220929, 1506674774),
(3, 'wechat', '/20170929/channel_937d52af3d8cd098f50f56cb0c6222e8.png', '/20170927/channel_2981de89d267449858bf1372c70a33f9.png', 6, 1506324640, 1506674789),
(4, 'facebook', '/20170929/channel_a3f3b71edf25a34000b495570a361c74.png', '/20170927/channel_3e199bd37cc8a05b053945a55fc00b51.png', 7, 1506324703, 1506674802),
(5, 'gmail', '/20170929/channel_19dd3cf42a1ce6c5fcd8e1a019db70f5.png', '/20170927/channel_29b96377c3258cc8d103ee0914bc64a6.png', 8, 1506324725, 1506674811),
(6, 'qq', '/20170929/channel_b55656fde3d74096c96ec9d637fd222e.png', '/20170927/channel_6c906c8ca2b2d1b3fa9d0d57c3b6fa9c.png', 5, 1506324746, 1506674823),
(7, 'skype', '/20170929/channel_065a40aa5ffe60e0822dcb9055528e6a.png', '/20170927/channel_59e881f7fa738bcbaf6fa688973ebf51.png', 3, 1506324759, 1506674843),
(8, 'whatsapp', '/20170929/channel_33b40e5acc7b7ded6771b720d515792e.png', '/20170927/channel_2b48733b686fc3cdebe28e20886cdb8a.png', 4, 1506324776, 1506674853);



ALTER TABLE `channel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel.sort` (`sort`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `channel`
--
ALTER TABLE `channel`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;




--
-- 表的结构 `customer`
--

CREATE TABLE `customer` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` char(32) NOT NULL DEFAULT '' COMMENT '客户编码',
  `name` char(32) NOT NULL DEFAULT '' COMMENT '客户主要名称',
  `number` char(32) NOT NULL DEFAULT '' COMMENT '客户代号',
  `aide_name` char(32) DEFAULT NULL COMMENT '辅助名称',
  `group_id` int(11) NOT NULL COMMENT '上级单位id',
  `level` int(11) NOT NULL COMMENT '级别',
  `type` int(11) NOT NULL COMMENT '客户类型',
  `company` char(32) DEFAULT NULL COMMENT '集团机构编号',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `admin_id` int(11) NOT NULL COMMENT '录入管理员的id',
  `update_id` int(11) NOT NULL,
  `create_at` int(11) NOT NULL,
  `update_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


 --
-- 使用表AUTO_INCREMENT `好友表`
--

  CREATE TABLE `friends` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `friend_id` int(11) NOT NULL COMMENT '好友id',
  `create_at` int(11) NOT NULL COMMENT '创建时间',
  `remark` char(64) DEFAULT '' COMMENT '备注',
  `extsion` text COMMENT '扩展信息json',
  `direction` tinyint(1) NOT NULL DEFAULT '0' COMMENT '请求方向：0主动发送请求，1接受好友请求',
  `is_new_friend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是新朋友：0是，1否',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户组id 0表示为分组',
  `link_time` int(11) UNSIGNED DEFAULT '0' COMMENT '最近联系时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;




  --
-- 表的结构 `friends_group`
--

CREATE TABLE `friends_group` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `group_name` char(128) NOT NULL DEFAULT '' COMMENT '组名',
  `create_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friends_group`
--
ALTER TABLE `friends_group`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `friends_group`
--
ALTER TABLE `friends_group`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;






  CREATE TABLE `friends_request` (
  `id` int(11) UNSIGNED NOT NULL,
  `from_id` int(11) NOT NULL COMMENT '发送请求者',
  `to_id` int(11) NOT NULL COMMENT '被请求者',
  `note` varchar(255) NOT NULL COMMENT '请求备注',
  `status` tinyint(1) NOT NULL COMMENT '状态：0:发送请求，1:同意，2:拒绝',
  `create_at` int(11) NOT NULL,
  `update_at` int(11) DEFAULT '0' COMMENT '更新时间',
  `is_new_invite` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是新邀请'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friends_request`
--
ALTER TABLE `friends_request`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `friends_request`
--
ALTER TABLE `friends_request`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;






  CREATE TABLE `message_catch` (
  `id` int(11) UNSIGNED NOT NULL,
  `ucode` int(11) NOT NULL COMMENT '用户的优码',
  `message` text COMMENT '消息内容',
  `begin_time` int(11) NOT NULL COMMENT '存入时间',
  `end_time` int(11) DEFAULT NULL COMMENT '过期时间',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '发送状态',
  `send_time` int(11) DEFAULT NULL COMMENT '最近一次发送的时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `message_catch`
--
ALTER TABLE `message_catch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ucode` (`ucode`),
  ADD KEY `status` (`status`);




  CREATE TABLE `question` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL COMMENT '题目',
  `create_at` int(11) NOT NULL,
  `update_at` int(11) NOT NULL,
  `type` smallint(3) NOT NULL COMMENT '题组：1:1组，2：2组，3:3组'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `question`
--

INSERT INTO `question` (`id`, `title`, `create_at`, `update_at`, `type`) VALUES
(1, '您上一间公司叫什么？', 1502097331, 1502097331, 1),
(2, '您最亲的人手机号后4位是什么?', 1502097331, 1502097331, 1),
(3, '您的小学全名叫什么?', 1502097331, 1502097331, 2),
(4, '圣经里您记得最清晰的一句话是什么？', 1502097331, 1502097331, 2),
(5, '手机里您最不会删除的程序是什么？', 1502097331, 1502097331, 3),
(6, '您网购买过最贵的商品是什么？', 1502097331, 1502097331, 3),
(7, '您的电脑硬盘多少G?', 1502097331, 1502097331, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `question`
--
ALTER TABLE `question`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;




  CREATE TABLE `security_question` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `userid` int(11) NOT NULL COMMENT '用户id',
  `q_one` smallint(5) NOT NULL COMMENT '第一个密保问题id(对应question表对应的id)',
  `a_one` varchar(255) NOT NULL COMMENT '第一个密保的答案',
  `q_two` smallint(5) NOT NULL COMMENT '第二个密保问题id',
  `a_two` varchar(255) NOT NULL COMMENT '第二个密保的答案',
  `q_three` smallint(5) NOT NULL COMMENT '第三个密保问题id',
  `a_three` varchar(255) NOT NULL COMMENT '第三个密保的答案'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `security_question`
--
ALTER TABLE `security_question`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `security_question`
--
ALTER TABLE `security_question`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;





CREATE TABLE `tmp_report_call` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` int(11) DEFAULT NULL COMMENT '电话类型 好友 非好友',
  `number` int(11) DEFAULT NULL COMMENT '次数',
  `day` date DEFAULT NULL COMMENT '统计时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tmp_report_call`
--
ALTER TABLE `tmp_report_call`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `tmp_report_call`
--
ALTER TABLE `tmp_report_call`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;




  CREATE TABLE `tts_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '日志类型',
  `url` char(255) DEFAULT '' COMMENT '交互的ip／url',
  `data` text COMMENT '交互的内容',
  `object` text COMMENT '交互的对象',
  `time` datetime DEFAULT NULL COMMENT '时间',
  `number` char(255) DEFAULT NULL COMMENT '电话号码',
  `call_id` char(255) DEFAULT NULL COMMENT '呼叫id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;







CREATE TABLE `user_login_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '登录用户id',
  `address` varchar(255) DEFAULT NULL COMMENT '登录地址',
  `longitude` varchar(64) DEFAULT NULL COMMENT '经度',
  `latitude` varchar(64) DEFAULT NULL COMMENT '纬度',
  `login_ip` varchar(64) DEFAULT NULL COMMENT '登录IP',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_login_log`
--
ALTER TABLE `user_login_log`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `user_login_log`
--
ALTER TABLE `user_login_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;





  --
-- 表的结构 `version`
--

CREATE TABLE `version` (
  `id` mediumint(9) UNSIGNED NOT NULL,
  `platform` varchar(64) NOT NULL COMMENT '平台:android,ios',
  `version` varchar(64) NOT NULL COMMENT '版本号',
  `info` text NOT NULL COMMENT '版本更新内容',
  `url` varchar(255) NOT NULL COMMENT '版本的地址',
  `create_at` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `version`
--
ALTER TABLE `version`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `version`
--
ALTER TABLE `version`
  MODIFY `id` mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT;
