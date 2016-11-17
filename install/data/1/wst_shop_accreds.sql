SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_shop_accreds`;
CREATE TABLE `wst_shop_accreds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accredId` int(11) NOT NULL DEFAULT '0',
  `shopId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shopId` (`shopId`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

INSERT INTO `wst_shop_accreds` VALUES ('2', '2', '3'),
('3', '2', '4'),
('5', '1', '5'),
('6', '1', '8'),
('7', '2', '8'),
('8', '1', '9'),
('9', '2', '9'),
('10', '1', '10'),
('11', '2', '10'),
('12', '1', '11'),
('19', '1', '1'),
('20', '2', '1');
