SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_shop_configs`;
CREATE TABLE `wst_shop_configs` (
  `configId` int(11) NOT NULL AUTO_INCREMENT,
  `shopId` int(11) NOT NULL,
  `shopTitle` varchar(255) NOT NULL,
  `shopKeywords` varchar(255) NOT NULL,
  `shopDesc` varchar(255) NOT NULL,
  `shopBanner` varchar(150) NOT NULL,
  `shopAds` text NOT NULL,
  `shopAdsUrl` text NOT NULL,
  `shopServicer` varchar(100) NOT NULL,
  `shopHotWords` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`configId`),
  KEY `shopId` (`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
