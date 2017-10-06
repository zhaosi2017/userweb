#呼叫记录表更新
CREATE TABLE call_record (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  active_call_uid int(11) NOT NULL COMMENT '主叫id',
  call_id char(64) DEFAULT NULL COMMENT '呼叫的id',
  unactive_call_uid int(11) NOT NULL COMMENT '被叫id',
  call_time int(11) DEFAULT NULL COMMENT '记录时间',
  text char(255) DEFAULT '' COMMENT '语音类容',
  duration int(4) DEFAULT NULL COMMENT '通话时间',
  amount decimal(14,4) DEFAULT NULL COMMENT '通话金额',
  status int(11) DEFAULT NULL COMMENT '通话状态',
  type int(11) DEFAULT NULL COMMENT '呼叫类型',
  contact_number varchar(64) DEFAULT NULL COMMENT '主叫电话',
  unactive_contact_number char(15) DEFAULT '' COMMENT '被叫电话',
  third char(255) DEFAULT NULL COMMENT '呼叫渠道',
  group_id char(64) DEFAULT NULL COMMENT '通话记录id',
  active_account varchar(100) NOT NULL DEFAULT '0' COMMENT '主叫账号（potato名称）',
  unactive_nickname varchar(50) DEFAULT '*' COMMENT '被叫昵称',
  unactive_account varchar(100) NOT NULL DEFAULT '0' COMMENT '被叫账号（potato名称）',
  active_nickname varchar(50) NOT NULL DEFAULT '*' COMMENT '主叫昵称',
  record_status int(11) DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;