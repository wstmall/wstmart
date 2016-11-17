SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_navs`;
CREATE TABLE `wst_navs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `navType` tinyint(4) NOT NULL DEFAULT '0',
  `navTitle` varchar(50) NOT NULL,
  `navUrl` varchar(100) NOT NULL,
  `isShow` tinyint(4) NOT NULL DEFAULT '1',
  `isOpen` tinyint(4) NOT NULL DEFAULT '0',
  `navSort` int(11) NOT NULL DEFAULT '0',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `navType` (`navType`,`isShow`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

 INSERT INTO `wst_navs` VALUES('1', '0', '品牌街', 'home/brands/index.html', '1', '1', '2', '2015-07-12 20:08:22'),
 ('2', '0', '首页', 'index.php', '1', '0', '0', '2015-07-12 20:08:36'),
 ('3', '0', '店铺街', 'home/shops/shopstreet.html', '1', '0', '3', '2015-07-12 20:10:00'),
 ('4', '0', '自营超市', 'home/shops/selfshop', '1', '0', '4', '2015-07-12 20:11:21'),
 ('5', '1', '关于我们', '#', '1', '0', '0', '2015-07-12 20:25:58'),
 ('7', '1', 'WST百科', '#', '1', '0', '0', '2015-07-12 23:02:39'),
 ('8', '1', '帮助中心', '#', '1', '0', '0', '2015-07-12 23:03:43'),
 ('9', '1', '交易条款', '#', '1', '0', '0', '2015-07-12 23:03:55'),
 ('10', '1', '诚征英才', '#', '1', '0', '0', '2015-07-12 23:04:41'),
 ('11', '1', '网站地图', '#', '1', '0', '0', '2015-07-12 23:04:51'),
 ('12', '1', '友情链接', '#', '0', '0', '0', '2015-07-12 23:05:08'),
 ('13', '1', '店铺管理', 'shop.php', '0', '0', '0', '2015-07-12 23:05:42'),
 ('15', '0', '时蔬水果', 'home/goods/lists/cat/47.html', '1', '0', '1', '2016-09-06 14:22:36'),
 ('16', '0', '厨房清洁', 'home/goods/lists/cat/48.html', '1', '0', '2', '2016-09-06 14:23:08'),
 ('17', '0', '床上家居', 'home/goods/lists/cat/54.html', '1', '0', '3', '2016-09-06 14:23:38'),
 ('18', '0', '养生之道', '2', '1', '1', '5', '2016-09-06 14:24:28');
