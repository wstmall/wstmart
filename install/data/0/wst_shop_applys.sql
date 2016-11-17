SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_shop_applys`;
CREATE TABLE `wst_shop_applys` (
  `applyId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT '0',
  `linkman` varchar(50) NOT NULL,
  `phoneNo` varchar(20) NOT NULL,
  `applyDesc` varchar(255) NOT NULL,
  `applyStatus` tinyint(4) NOT NULL DEFAULT '0',
  `handleDesc` text,
  `shopId` int(11) DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`applyId`),
  KEY `applyStatus` (`applyStatus`,`dataFlag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

