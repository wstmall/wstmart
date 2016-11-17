SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_cat_brands`;
CREATE TABLE `wst_cat_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catId` int(11) DEFAULT NULL,
  `brandId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catId` (`catId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

