SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_staffs`;
CREATE TABLE `wst_staffs` (
  `staffId` int(11) NOT NULL AUTO_INCREMENT,
  `loginName` varchar(40) NOT NULL,
  `loginPwd` varchar(50) NOT NULL,
  `secretKey` int(32) NOT NULL,
  `staffName` varchar(50) NOT NULL,
  `staffNo` varchar(20) DEFAULT NULL,
  `staffPhoto` varchar(150) DEFAULT NULL,
  `staffRoleId` int(11) NOT NULL,
  `workStatus` tinyint(4) NOT NULL DEFAULT '1',
  `staffStatus` tinyint(4) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  `lastTime` datetime DEFAULT NULL,
  `lastIP` char(16) DEFAULT NULL,
  PRIMARY KEY (`staffId`),
  KEY `loginName` (`loginName`),
  KEY `staffStatus` (`staffStatus`,`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

INSERT INTO `wst_staffs` VALUES ('1', 'admin', 'd2f99c5faf90fcf28bdac65d81a6e741', '9365', 'admin', '001', 'Upload/staffs/2015-04/55306cf76bc1f.jpg', '3', '1', '1', '1', '2014-04-06 11:47:20', '2016-10-17 10:03:54', '113.109.180.6'),
 ('2', 'system', 'a0da805e0b77f6cc05cdf0ef6ca8caad', '2508', '系统管理员', 'sn001', null, '3', '1', '1', '1', '2014-12-20 00:13:36', null, null),
 ('3', 'goodsAdmin', '1600195af828b21c1f586b1e01cb89fc', '1729', '商品管理员', 'sn001', 'Upload/staffs/2014-12/5496376a7ff89.jpg', '1', '1', '1', '1', '2014-12-21 10:58:40', null, null),
 ('4', 'rrr', '07835ecd178ee79ef0cfdb8240c18364', '8871', 'rrr', 'rrr', '\\upload\\staffs\\2016-08\\88\\e3b5fcacf9fb3c51b8cb5a036a2bf8.jpg', '0', '1', '1', '-1', '2016-08-12 23:57:41', null, null),
 ('5', 'ttt', '84199b9eb283d7c5be45a1f590d4a08f', '7982', 'ttt', 'ttt', '/upload/staffs/2016-08\\59\\099bfb349c4a7694c477aa94f23664.jpg', '0', '1', '1', '1', '2016-08-12 23:59:19', null, null),
 ('6', 'rrrcc', '17059e82870edb4e0320d52a40096519', '8333', 'rrr', 'rrr', '/upload/staffs/2016-08\\c2\\28f39b9a0cdd5839613f8aa6ef8256.jpg', '0', '1', '1', '1', '2016-08-13 00:20:48', null, null),
 ('7', 'rrr', 'd1ddbff25d00debf3ec48dcd541b7604', '5173', 'rrr', 'rr', '', '0', '1', '1', '1', '2016-08-13 00:23:02', null, null),
 ('8', 'rrrv', '79a65611f151432a56aca6cf291f3aff', '2294', 'rr', 'rr', '', '0', '1', '1', '1', '2016-08-13 00:23:15', null, null),
 ('9', 'dddddddddddddddddddd', 'c54a53d5764e413b33cfaba89a06d164', '4832', 'ddd--', 'dd--', '/upload/staffs/2016-08\\88\\e3b5fcacf9fb3c51b8cb5a036a2bf8.jpg', '2', '1', '1', '-1', '2016-08-13 00:24:32', null, null),
 ('10', 'fffff', '561429601f590b45f65e150b6a1daf5f', '3408', 'ffff', '', '', '0', '1', '1', '1', '2016-08-18 12:50:55', null, null);
