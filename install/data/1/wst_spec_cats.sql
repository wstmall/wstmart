SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_spec_cats`;
CREATE TABLE `wst_spec_cats` (
  `catId` int(11) NOT NULL AUTO_INCREMENT,
  `goodsCatId` int(11) NOT NULL DEFAULT '0',
  `goodsCatPath` varchar(100) NOT NULL,
  `catName` varchar(255) NOT NULL,
  `isAllowImg` tinyint(4) NOT NULL DEFAULT '0',
  `isShow` tinyint(4) NOT NULL DEFAULT '1',
  `catSort` int(11) DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`catId`),
  KEY `shopId` (`goodsCatPath`,`dataFlag`),
  KEY `isShow` (`isShow`,`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `wst_spec_cats` VALUES ('1', '351', '334_348_351_', '手机版本', '0', '1', '0', '1', '2016-10-08 10:46:20'),
('2', '351', '334_348_351_', '屏幕大小', '0', '1', '0', '1', '2016-10-08 10:47:05'),
('3', '351', '334_348_351_', '内存大小', '0', '1', '0', '1', '2016-10-08 10:47:35'),
('4', '351', '334_348_351_', '套装', '0', '1', '0', '1', '2016-10-08 10:51:01'),
('5', '351', '334_348_351_', '购买方式', '0', '1', '0', '1', '2016-10-08 10:51:22'),
('6', '351', '334_348_351_', '选择颜色', '1', '1', '0', '1', '2016-10-08 10:52:02');
