SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `wst_brands`;
CREATE TABLE `wst_brands` (
  `brandId` int(11) NOT NULL AUTO_INCREMENT,
  `brandName` varchar(100) NOT NULL,
  `brandImg` varchar(150) NOT NULL,
  `brandDesc` text,
  `createTime` datetime NOT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`brandId`),
  KEY `brandFlag` (`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `wst_brands` VALUES ('1', '华为', 'upload/brands/2016-10/57f860e20d7ae.jpg', '华为 是华为公司旗下的一款手机品牌', '2016-10-08 10:59:01', '1'),
('2', '维达', 'upload/brands/2016-10/57fa2e3aeb270.png', '我是一个维达，wstmart下的维达', '2016-10-09 19:47:22', '1'),
('3', '威露士', 'upload/brands/2016-10/57fa441b3131f.jpg', '威露士 wstmart', '2016-10-09 21:20:36', '1'),
('4', '蓝月亮', 'upload/brands/2016-10/57fa464b09db9.jpg', 'wstmart 蓝月亮', '2016-10-09 21:30:02', '1'),
('5', '雕牌', 'upload/brands/2016-10/57fa4d66e1fe5.jpg', '<p>\n	这是一个wstmart开源商城的一个商品品牌\n</p>\n<p>\n	雕牌\n</p>', '2016-10-09 22:00:37', '1'),
('6', '青岛啤酒', 'upload/brands/2016-10/57faf58d010f9.jpg', 'wstmart青岛啤酒品牌', '2016-10-10 09:57:41', '1'),
('7', 'Sisley希思黎', 'upload/brands/2016-10/57fb4d8bb8c72.jpg', '<h1 style=\"font-size:16px;font-family:\'microsoft yahei\';background-color:#FFFFFF;\">\n	Sisley希思黎\n</h1>', '2016-10-10 16:13:04', '1'),
('8', '福临门', 'upload/brands/2016-10/57fb5673dadba.jpg', '福临门花生油', '2016-10-10 16:51:08', '1'),
('9', '鲁花花生油', 'upload/brands/2016-10/57fb56967f5c0.jpg', '鲁花花生油 wstmart', '2016-10-10 16:51:47', '1'),
('10', '金龙鱼', 'upload/brands/2016-10/57fb56b259418.jpg', '金龙鱼', '2016-10-10 16:52:07', '1'),
('11', '华为荣耀', 'upload/brands/2016-10/57fc8462e16f3.jpg', '荣耀，华为手机子品牌', '2016-10-11 14:19:28', '1');
