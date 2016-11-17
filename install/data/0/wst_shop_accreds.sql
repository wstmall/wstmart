SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_shop_accreds`;
CREATE TABLE `wst_shop_accreds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accredId` int(11) NOT NULL DEFAULT '0',
  `shopId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shopId` (`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
