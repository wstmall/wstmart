SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_order_complains`;
CREATE TABLE `wst_order_complains` (
  `complainId` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) NOT NULL DEFAULT '0',
  `complainType` tinyint(4) NOT NULL DEFAULT '1',
  `complainTargetId` int(11) NOT NULL DEFAULT '0',
  `respondTargetId` int(11) NOT NULL DEFAULT '0',
  `needRespond` tinyint(4) NOT NULL DEFAULT '0',
  `deliverRespondTime` datetime DEFAULT NULL,
  `complainContent` text NOT NULL,
  `complainAnnex` varchar(255) DEFAULT NULL,
  `complainStatus` tinyint(4) NOT NULL DEFAULT '0',
  `complainTime` datetime NOT NULL,
  `respondContent` text,
  `respondAnnex` varchar(255) DEFAULT NULL,
  `respondTime` datetime DEFAULT NULL,
  `finalResult` text,
  `finalResultTime` datetime DEFAULT NULL,
  `finalHandleStaffId` int(11) DEFAULT '0',
  PRIMARY KEY (`complainId`),
  KEY `complainStatus` (`complainStatus`),
  KEY `complainType` (`complainTargetId`,`complainType`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

