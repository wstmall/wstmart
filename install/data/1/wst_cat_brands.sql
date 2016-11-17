SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_cat_brands`;
CREATE TABLE `wst_cat_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catId` int(11) DEFAULT NULL,
  `brandId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catId` (`catId`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

INSERT INTO `wst_cat_brands` VALUES ('2', '48', '2'),
('3', '48', '3'),
('4', '48', '4'),
('5', '48', '5'),
('6', '49', '6'),
('7', '51', '7'),
('8', '50', '8'),
('9', '50', '9'),
('10', '50', '10'),
('11', '334', '1'),
('12', '334', '11');
