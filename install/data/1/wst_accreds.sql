SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_accreds`;
CREATE TABLE `wst_accreds` (
  `accredId` int(11) NOT NULL AUTO_INCREMENT,
  `accredName` varchar(50) NOT NULL,
  `accredImg` varchar(150) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`accredId`),
  KEY `dataFlag` (`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `wst_accreds` VALUES ('1', '消保认证商家', 'upload/accreds/2016-09/57edd7551cf4a.png', '1', '2016-06-01 10:41:48'),
('2', '七天无条件退款', 'upload/accreds/2016-09/57edd7428f5e1.png', '1', '2016-06-01 10:42:22');
