SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_data_cats`;
CREATE TABLE `wst_data_cats` (
  `catId` int(11) NOT NULL AUTO_INCREMENT,
  `catName` varchar(255) NOT NULL,
  PRIMARY KEY (`catId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `wst_data_cats` VALUES ('1', '订单取消原因'),
('2', '订单投诉原因'),
('3', '上传目录列表'),
('4', '申请退款原因'),
('5', '广告类型');
