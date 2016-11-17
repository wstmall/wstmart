SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_order_refunds`;
CREATE TABLE `wst_order_refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) NOT NULL,
  `refundTo` int(11) NOT NULL DEFAULT '0',
  `refundReson` int(11) NOT NULL DEFAULT '0',
  `refundOtherReson` varchar(255) DEFAULT NULL,
  `backMoney` decimal(11,2) NOT NULL DEFAULT '0.00',
  `refundTradeNo` varchar(100) DEFAULT NULL,
  `refundRemark` varchar(400) NOT NULL,
  `refundTime` datetime NOT NULL,
  `shopRejectReason` varchar(255) DEFAULT NULL,
  `refundStatus` tinyint(4) NOT NULL DEFAULT '0',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderId_2` (`orderId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;