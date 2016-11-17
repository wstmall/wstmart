SET FOREIGN_KEY_CHECKS=0;


DROP TABLE IF EXISTS `wst_images`;
CREATE TABLE `wst_images` (
  `imgId` int(11) NOT NULL AUTO_INCREMENT,
  `fromType` tinyint(4) NOT NULL DEFAULT '0',
  `dataId` int(11) NOT NULL DEFAULT '0',
  `imgPath` varchar(150) NOT NULL,
  `imgSize` int(11) NOT NULL DEFAULT '0',
  `isUse` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  `fromTable` varchar(50) DEFAULT NULL,
  `ownId` int(11) DEFAULT NULL,
  `dataFlag` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`imgId`),
  KEY `isUse` (`isUse`,`fromTable`,`dataFlag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

