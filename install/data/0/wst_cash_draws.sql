SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_cash_draws`;
CREATE TABLE `wst_cash_draws` (
  `cashId` int(11) NOT NULL AUTO_INCREMENT,
  `cashNo` varchar(50) NOT NULL,
  `targetType` tinyint(4) NOT NULL DEFAULT '0',
  `targetId` int(11) NOT NULL DEFAULT '0',
  `money` decimal(11,2) NOT NULL DEFAULT '0.00',
  `accType` tinyint(4) NOT NULL DEFAULT '0',
  `accTargetName` varchar(100) DEFAULT NULL,
  `accAreaName` varchar(100) DEFAULT NULL,
  `accNo` varchar(100) NOT NULL,
  `accUser` varchar(100) DEFAULT NULL,
  `cashSatus` tinyint(4) NOT NULL DEFAULT '0',
  `cashRemarks` varchar(255) DEFAULT NULL,
  `cashConfigId` int(11) NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`cashId`),
  KEY `targetType` (`targetType`,`targetId`),
  KEY `cashNo` (`cashNo`)
) ENGINE=InnoDB AUTO_INCREMENT=10000000 DEFAULT CHARSET=utf8;
