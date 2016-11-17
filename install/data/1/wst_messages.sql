SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_messages`;
CREATE TABLE `wst_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msgType` tinyint(4) NOT NULL DEFAULT '0',
  `sendUserId` int(11) NOT NULL DEFAULT '0',
  `receiveUserId` int(11) NOT NULL DEFAULT '0',
  `msgContent` text NOT NULL,
  `msgStatus` tinyint(4) NOT NULL DEFAULT '0',
  `msgJson` varchar(255) DEFAULT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `receiveUserId` (`receiveUserId`,`dataFlag`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;