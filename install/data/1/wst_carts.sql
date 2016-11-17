SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_carts`;
CREATE TABLE `wst_carts` (
  `cartId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL DEFAULT '0',
  `isCheck` tinyint(4) NOT NULL DEFAULT '1',
  `goodsId` int(11) NOT NULL DEFAULT '0',
  `goodsSpecId` varchar(200) NOT NULL DEFAULT '0',
  `cartNum` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cartId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
