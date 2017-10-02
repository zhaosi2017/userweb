#消息缓存表
CREATE TABLE `message_catch` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ucode` int(11) NOT NULL COMMENT '用户的优码',
  `message` text COMMENT '消息内容',
  `begin_time` int(11) NOT NULL COMMENT '存入时间',
  `end_time` int(11) DEFAULT NULL COMMENT '过期时间',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '发送状态',
  `send_time` int(11) DEFAULT NULL COMMENT '最近一次发送的时间',
  PRIMARY KEY (`id`),
  KEY `ucode` (`ucode`),
  KEY `end_time` (`end_time`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;