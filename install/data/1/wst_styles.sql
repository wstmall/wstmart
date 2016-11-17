SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_styles`;
CREATE TABLE `wst_styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `styleSys` tinyint(4) NOT NULL DEFAULT '0',
  `styleName` varchar(255) NOT NULL,
  `styleAuthor` varchar(255) DEFAULT NULL,
  `styleShopSite` varchar(11) DEFAULT NULL,
  `styleShopId` int(11) DEFAULT '0',
  `stylePath` varchar(255) NOT NULL,
  `isUse` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `isUse` (`isUse`,`styleSys`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `wst_styles` VALUES ('1', '0', '默认模板', 'WSTMart开发组', '', '1', 'default', '1');
