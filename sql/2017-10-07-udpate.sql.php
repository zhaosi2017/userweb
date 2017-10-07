
/**呼叫记录***/
ALTER TABLE `call_record` ADD `call_id` CHAR(64) NULL DEFAULT NULL AFTER `active_nickname`, ADD `text` CHAR(255) NULL DEFAULT NULL AFTER `call_id`, ADD `duration` INT(4) NOT NULL DEFAULT '0' AFTER `text`, ADD `amount` DECIMAL(14,4) NULL DEFAULT NULL AFTER `duration`, ADD `group_id` CHAR(64) NULL DEFAULT NULL AFTER `amount`, ADD `third` CHAR(255) NULL DEFAULT NULL AFTER `group_id`;



/***user表***/
ALTER TABLE `user` ADD `email` VARCHAR(255) NULL DEFAULT NULL COMMENT '邮箱' AFTER `nickname`, ADD `token` VARCHAR(255) NULL DEFAULT NULL COMMENT '令牌' AFTER `email`, ADD `channel` VARCHAR(255) NULL DEFAULT NULL COMMENT '渠道' AFTER `token`, ADD `address` VARCHAR(255) NULL DEFAULT NULL COMMENT '注册地址' AFTER `channel`, ADD `longitude` VARCHAR(64) NULL DEFAULT NULL COMMENT '注册经度' AFTER `address`, ADD `latitude` VARCHAR(64) NULL DEFAULT NULL COMMENT '注册纬度' AFTER `longitude`, ADD `header_img` VARCHAR(255) NULL DEFAULT NULL COMMENT '头像地址' AFTER `latitude`, ADD `balance` DECIMAL(14,4) NULL DEFAULT '0.0000' COMMENT '余额' AFTER `header_img`;