SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_cat_shops`;
CREATE TABLE `wst_cat_shops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopId` int(11) NOT NULL DEFAULT '0',
  `catId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `catId` (`catId`),
  KEY `shopId` (`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

