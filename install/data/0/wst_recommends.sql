SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_recommends`;
CREATE TABLE `wst_recommends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodsCatId` int(11) NOT NULL DEFAULT '0',
  `dataType` tinyint(4) NOT NULL DEFAULT '0',
  `dataSrc` tinyint(4) DEFAULT '0',
  `dataId` int(11) NOT NULL DEFAULT '0',
  `dataSort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `goodsCatId` (`goodsCatId`,`dataType`,`dataSrc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
