#呼叫交互数据日志记录
CREATE TABLE `tts_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '日志类型',
  `url` char(255) DEFAULT '' COMMENT '交互的ip／url',
  `data` text COMMENT '交互的内容',
  `object` text COMMENT '交互的对象',
  `time` datetime DEFAULT NULL COMMENT '时间',
  `number` char(255) DEFAULT NULL COMMENT '电话号码',
  `call_id` char(255) DEFAULT NULL COMMENT '呼叫id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;