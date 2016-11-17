SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_user_ranks`;
CREATE TABLE `wst_user_ranks` (
  `rankId` int(11) NOT NULL AUTO_INCREMENT,
  `rankName` varchar(20) NOT NULL,
  `startScore` int(11) NOT NULL DEFAULT '0',
  `endScore` int(11) NOT NULL DEFAULT '0',
  `rebate` int(11) NOT NULL DEFAULT '100',
  `userrankImg` varchar(150) DEFAULT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`rankId`),
  KEY `startScore` (`startScore`,`endScore`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `wst_user_ranks` VALUES ('1', '初级会员', '0', '500', '0', 'upload/userranks/2016-09/57edd6ae5d8b8.png', '1', '2016-08-14 19:26:18'),
('2', '中级会员', '501', '1000', '0', 'upload/userranks/2016-09/57edd6d446b75.png', '1', '2016-08-14 19:26:28'),
('3', '高级会员', '1001', '3000', '0', 'upload/userranks/2016-09/57edd6efc8757.png', '1', '2016-08-16 17:00:35'),
('4', '钻石会员', '3001', '100000', '0', 'upload/userranks/2016-09/57edd70cbd0f5.png', '1', '2016-08-24 10:40:13');
