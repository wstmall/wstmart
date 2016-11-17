SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_attributes`;
CREATE TABLE `wst_attributes` (
  `attrId` int(11) NOT NULL AUTO_INCREMENT,
  `goodsCatId` int(11) NOT NULL DEFAULT '0',
  `goodsCatPath` varchar(100) NOT NULL,
  `attrName` varchar(100) NOT NULL,
  `attrType` tinyint(4) NOT NULL DEFAULT '0',
  `attrVal` text,
  `attrSort` int(11) NOT NULL DEFAULT '0',
  `isShow` tinyint(4) DEFAULT '1',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`attrId`),
  KEY `shopId` (`goodsCatId`,`isShow`,`dataFlag`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

