SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_shop_freights`;
CREATE TABLE `wst_shop_freights` (
  `freightId` int(11) NOT NULL AUTO_INCREMENT,
  `shopId` int(11) NOT NULL,
  `areaId2` int(11) NOT NULL,
  `freight` int(11) NOT NULL DEFAULT '0',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`freightId`),
  KEY `shopId` (`shopId`,`areaId2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

