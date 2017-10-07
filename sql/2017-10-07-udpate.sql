
/**呼叫记录***/
ALTER TABLE `call_record` ADD `call_id` CHAR(64) NULL DEFAULT NULL AFTER `active_nickname`, ADD `text` CHAR(255) NULL DEFAULT NULL AFTER `call_id`, ADD `duration` INT(4) NOT NULL DEFAULT '0' AFTER `text`, ADD `amount` DECIMAL(14,4) NULL DEFAULT NULL AFTER `duration`, ADD `group_id` CHAR(64) NULL DEFAULT NULL AFTER `amount`, ADD `third` CHAR(255) NULL DEFAULT NULL AFTER `group_id`;



/***user表***/
ALTER TABLE `user` ADD `email` VARCHAR(255) NULL DEFAULT NULL COMMENT '邮箱' AFTER `nickname`, ADD `token` VARCHAR(255) NULL DEFAULT NULL COMMENT '令牌' AFTER `email`, ADD `channel` VARCHAR(255) NULL DEFAULT NULL COMMENT '渠道' AFTER `token`, ADD `address` VARCHAR(255) NULL DEFAULT NULL COMMENT '注册地址' AFTER `channel`, ADD `longitude` VARCHAR(64) NULL DEFAULT NULL COMMENT '注册经度' AFTER `address`, ADD `latitude` VARCHAR(64) NULL DEFAULT NULL COMMENT '注册纬度' AFTER `longitude`, ADD `header_img` VARCHAR(255) NULL DEFAULT NULL COMMENT '头像地址' AFTER `latitude`, ADD `balance` DECIMAL(14,4) NULL DEFAULT '0.0000' COMMENT '余额' AFTER `header_img`;

#统计好友电话
CREATE TABLE `tmp_report_call` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`type` int(11) DEFAULT NULL COMMENT '电话类型 好友 非好友',
`number` int(11) DEFAULT NULL COMMENT '次数',
`day` date DEFAULT NULL COMMENT '统计时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;