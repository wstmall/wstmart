SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_log_sms`;
CREATE TABLE `wst_log_sms` (
  `smsId` int(11) NOT NULL AUTO_INCREMENT,
  `smsSrc` tinyint(4) NOT NULL DEFAULT '0',
  `smsUserId` int(11) NOT NULL DEFAULT '0',
  `smsContent` varchar(255) NOT NULL,
  `smsPhoneNumber` varchar(11) NOT NULL,
  `smsReturnCode` varchar(255) NOT NULL,
  `smsCode` varchar(20) NOT NULL,
  `smsFunc` varchar(50) NOT NULL,
  `smsIP` varchar(16) NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`smsId`),
  KEY `smsPhoneNumber` (`smsPhoneNumber`),
  KEY `smsIP` (`smsIP`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

