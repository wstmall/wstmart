SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `wst_article_cats`;
CREATE TABLE `wst_article_cats` (
  `catId` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) NOT NULL DEFAULT '0',
  `catType` tinyint(4) NOT NULL DEFAULT '0',
  `isShow` tinyint(4) NOT NULL DEFAULT '1',
  `catName` varchar(20) NOT NULL,
  `catSort` int(11) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`catId`),
  KEY `isShow` (`catType`,`dataFlag`,`isShow`) USING BTREE,
  KEY `parentId` (`dataFlag`,`parentId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


INSERT INTO `wst_article_cats` VALUES ('1', '7', '1', '1', '支付方式', '2', '1', '2016-08-16 00:09:50'),
('5', '7', '1', '1', '购物指南', '0', '1', '2016-08-25 09:45:45'),
('6', '7', '1', '1', '商城快讯', '5', '1', '2016-09-06 15:21:09'),
('7', '0', '1', '1', '帮助中心', '6', '1', '2016-09-06 15:21:24'),
('8', '0', '0', '1', '商城快讯', '4', '1', '2016-09-06 15:21:51'),
('9', '7', '1', '1', '售后服务', '1', '1', '2016-09-06 15:22:00'),
('10', '7', '1', '1', '商务合作', '3', '1', '2016-09-06 15:24:35'),
('11', '8', '0', '1', '商城公告', '0', '1', '2016-09-26 23:04:18'),
('12', '8', '0', '1', '促销信息', '0', '1', '2016-09-26 23:04:25');
