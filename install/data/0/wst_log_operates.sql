SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_log_operates`;
CREATE TABLE `wst_log_operates` (
  `operateId` int(11) NOT NULL AUTO_INCREMENT,
  `staffId` int(11) NOT NULL DEFAULT '0',
  `operateTime` datetime NOT NULL,
  `menuId` int(11) NOT NULL,
  `operateDesc` varchar(255) NOT NULL,
  `operateUrl` varchar(255) NOT NULL,
  `content` text,
  `operateIP` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`operateId`),
  KEY `operateTime` (`staffId`,`menuId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;