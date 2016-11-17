SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_user_address`;
CREATE TABLE `wst_user_address` (
  `addressId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `userPhone` varchar(20) DEFAULT NULL,
  `areaIdPath` varchar(255) NOT NULL DEFAULT '0',
  `areaId` int(11) NOT NULL DEFAULT '0',
  `userAddress` varchar(255) NOT NULL,
  `isDefault` tinyint(4) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`addressId`),
  KEY `userId` (`userId`,`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT INTO `wst_user_address` VALUES ('1', '2', '张无忌', '138733232342', '440000_440100_440106_', '440106', '燕岭路燕侨大厦1708', '0', '1', '2016-10-09 10:36:51'),
('2', '19', '胡世春', '18710786643', '110000_110100_110105_', '110105', '望京启明国际大厦', '0', '1', '2016-10-12 16:06:19'),
('3', '21', '马生', '123123123', '440000_440100_440106_', '440106', 'asfas', '0', '1', '2016-10-13 18:04:21'),
('4', '21', '马生二', '189099999999', '440000_440100_440118_', '440118', '这个是第二个测试地址', '0', '1', '2016-10-13 18:48:40'),
('5', '21', '测试的第三个', '189099999999', '360000_361000_361002_', '361002', '测试的第三个地址', '0', '1', '2016-10-13 18:49:28'),
('6', '22', 'test', '112343434', '110000_110100_', '110100', 'tett', '0', '1', '2016-10-13 20:30:07'),
('7', '25', 'Marky', '13788888888', '110000_110100_110116_', '110116', '天安门8号', '1', '1', '2016-10-14 11:00:47');
