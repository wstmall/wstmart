SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_settlements`;
CREATE TABLE `wst_settlements` (
  `settlementId` int(11) NOT NULL AUTO_INCREMENT,
  `settlementNo` varchar(20) NOT NULL,
  `settlementType` tinyint(4) NOT NULL DEFAULT '0',
  `shopId` int(11) NOT NULL,
  `accName` varchar(100) NOT NULL,
  `accNo` varchar(50) NOT NULL,
  `accUser` varchar(100) NOT NULL,
  `areaName` varchar(100) NOT NULL,
  `settlementMoney` decimal(11,2) NOT NULL DEFAULT '0.00',
  `commissionFee` decimal(11,2) NOT NULL DEFAULT '0.00',
  `backMoney` decimal(11,2) NOT NULL DEFAULT '0.00',
  `settlementStatus` tinyint(4) NOT NULL DEFAULT '0',
  `settlementTime` datetime DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`settlementId`),
  KEY `shopId` (`shopId`),
  KEY `settlementStatus` (`settlementStatus`)
) ENGINE=InnoDB AUTO_INCREMENT=10000000 DEFAULT CHARSET=utf8;
