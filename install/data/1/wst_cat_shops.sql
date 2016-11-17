SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_cat_shops`;
CREATE TABLE `wst_cat_shops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopId` int(11) NOT NULL DEFAULT '0',
  `catId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `catId` (`catId`),
  KEY `shopId` (`shopId`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

INSERT INTO `wst_cat_shops` VALUES ('2', '2', '47'),
('3', '3', '47'),
('5', '4', '48'),
('6', '5', '48'),
('7', '6', '49'),
('8', '7', '49'),
('9', '8', '52'),
('10', '9', '51'),
('11', '10', '53'),
('12', '11', '334'),
('49', '1', '47'),
('50', '1', '48'),
('51', '1', '49'),
('52', '1', '50'),
('53', '1', '51'),
('54', '1', '54'),
('55', '1', '334'),
('56', '1', '52'),
('57', '1', '53'),
('58', '1', '55'),
('59', '1', '335'),
('60', '1', '56');
