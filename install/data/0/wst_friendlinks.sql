SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_friendlinks`;
CREATE TABLE `wst_friendlinks` (
  `friendlinkId` int(11) NOT NULL AUTO_INCREMENT,
  `friendlinkIco` varchar(150) DEFAULT '',
  `friendlinkName` varchar(50) NOT NULL DEFAULT '',
  `friendlinkUrl` varchar(150) NOT NULL DEFAULT '',
  `friendlinkSort` int(11) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`friendlinkId`),
  KEY `dataFlag` (`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `wst_friendlinks` VALUES ('1', '', 'WSTMart多商户商城', 'http://www.wstmart.net', '0', '1', '2016-10-20 11:53:56'),
 ('2', '', '商淘软件', 'http://www.shangtaosoft.com', '3', '1', '2016-10-20 11:53:56'),
 ('3', '', 'WSTMall社区O2O系统', 'http://www.wstmall.net', '4', '1', '2016-10-20 11:53:56'),
 ('4', '', 'WSTMart论坛', 'http://bbs.shangtaosoft.com', '2', '1', '2016-10-20 11:53:56'),
 ('5', '', 'WSTMall论坛', 'http://bbs.shangtaosoft.com', '5', '1', '2016-10-20 11:53:56');
