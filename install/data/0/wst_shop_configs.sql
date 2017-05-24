SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_shop_configs`;
CREATE TABLE `wst_shop_configs` (
  `configId` int(11) NOT NULL AUTO_INCREMENT,
  `shopId` int(11) NOT NULL,
  `shopTitle` varchar(255) DEFAULT NULL,
  `shopKeywords` varchar(255) DEFAULT NULL,
  `shopDesc` varchar(255) DEFAULT NULL,
  `shopBanner` varchar(150) DEFAULT NULL,
  `shopAds` text,
  `shopAdsUrl` text,
  `shopServicer` varchar(100) DEFAULT NULL,
  `shopHotWords` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`configId`),
  KEY `shopId` (`shopId`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;