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
  `token` VARCHAR(255)  DEFAULT NULL ,
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

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `remark` text,
  `create_id` int(10) unsigned NOT NULL DEFAULT '0',
  `update_id` int(10) unsigned NOT NULL DEFAULT '0',
  `create_at` int(11) DEFAULT '0',
  `update_at` int(11) DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;