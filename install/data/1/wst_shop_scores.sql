SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_shop_scores`;
CREATE TABLE `wst_shop_scores` (
  `scoreId` int(11) NOT NULL AUTO_INCREMENT,
  `shopId` int(11) NOT NULL,
  `totalScore` int(11) NOT NULL DEFAULT '0',
  `totalUsers` int(11) NOT NULL DEFAULT '0',
  `goodsScore` int(11) NOT NULL DEFAULT '0',
  `goodsUsers` int(11) NOT NULL DEFAULT '0',
  `serviceScore` int(11) NOT NULL DEFAULT '0',
  `serviceUsers` int(11) NOT NULL DEFAULT '0',
  `timeScore` int(11) NOT NULL DEFAULT '0',
  `timeUsers` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`scoreId`),
  UNIQUE KEY `shopId` (`shopId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `wst_shop_scores` VALUES ('1', '1', '12', '1', '4', '1', '4', '1', '4', '1'),
('2', '2', '24', '2', '8', '2', '8', '2', '8', '2'),
('3', '3', '0', '0', '0', '0', '0', '0', '0', '0'),
('4', '4', '0', '0', '0', '0', '0', '0', '0', '0'),
('5', '5', '0', '0', '0', '0', '0', '0', '0', '0'),
('6', '6', '0', '0', '0', '0', '0', '0', '0', '0'),
('7', '7', '0', '0', '0', '0', '0', '0', '0', '0'),
('8', '8', '0', '0', '0', '0', '0', '0', '0', '0'),
('9', '9', '0', '0', '0', '0', '0', '0', '0', '0'),
('10', '10', '0', '0', '0', '0', '0', '0', '0', '0'),
('11', '11', '0', '0', '0', '0', '0', '0', '0', '0');
