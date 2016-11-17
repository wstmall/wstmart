SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_banks`;
CREATE TABLE `wst_banks` (
  `bankId` int(11) NOT NULL AUTO_INCREMENT,
  `bankName` varchar(50) NOT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`bankId`),
  KEY `bankFlag` (`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

INSERT INTO `wst_banks` VALUES ('1', 'ffff', '-1', '2016-08-14 11:19:27'),
('17', '中国工商银行', '1', '2016-05-20 11:19:27'),
('18', '中国农业银行', '1', '2016-05-20 11:19:27'),
('19', '中国人民银行', '1', '2016-05-20 11:19:27'),
('20', '招商银行', '1', '2016-05-20 11:19:27'),
('21', '中兴银行', '1', '2016-05-20 11:19:27'),
('22', '交通银行', '1', '2016-05-20 11:19:27'),
('23', '深圳发展银行', '1', '2016-05-20 11:19:27'),
('24', '中国光大银行', '1', '2016-05-20 11:19:27');
