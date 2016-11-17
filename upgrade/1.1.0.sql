SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `wst_styles`;
CREATE TABLE `wst_styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `styleSys` tinyint(4) NOT NULL DEFAULT '0',
  `styleName` varchar(255) NOT NULL,
  `styleAuthor` varchar(255) DEFAULT NULL,
  `styleShopSite` varchar(11) DEFAULT NULL,
  `styleShopId` int(11) DEFAULT '0',
  `stylePath` varchar(255) NOT NULL,
  `isUse` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `isUse` (`isUse`,`styleSys`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `wst_styles` VALUES ('1', '0', '默认模板', 'WSTMart开发组', '', '1', 'default', '1');

alter table wst_goods_cats add column commissionRate decimal(11,2) DEFAULT -1;
alter table wst_order_goods add column commissionRate decimal(11,2) DEFAULT 0;
alter table wst_shops add column shopMoney decimal(11,2) DEFAULT 0;
alter table wst_shops add column lockMoney decimal(11,2) DEFAULT 0;
alter table wst_orders add column settlementId int DEFAULT 0;
alter table wst_orders add column `commissionFee` decimal(11,2) DEFAULT 0;
alter table wst_users drop column wxOpenId;
alter table wst_users drop column qqOpenId;
alter table wst_users add column payPwd varchar(100);
alter table wst_goods_cats drop column priceSection;
alter table wst_shops add column noSettledOrderNum int DEFAULT 0;
alter table wst_shops add column noSettledOrderFee decimal(11,2) DEFAULT 0;
alter table wst_shops add column paymentMoney decimal(11,2) DEFAULT 0;
alter table wst_shops add column bankAreaId int DEFAULT 0;
alter table wst_shops add column bankAreaIdPath varchar(100);
alter table wst_orders drop column shopRejectReason;

DROP TABLE IF EXISTS `wst_cash_configs`;
CREATE TABLE `wst_cash_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetType` tinyint(4) NOT NULL DEFAULT '0',
  `targetId` int(11) NOT NULL,
  `accType` tinyint(4) NOT NULL DEFAULT '0',
  `accTargetId` int(11) NOT NULL DEFAULT '0',
  `accAreaId` int(11) DEFAULT NULL,
  `accNo` varchar(100) NOT NULL,
  `accUser` varchar(100) NOT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `targetType` (`targetType`,`targetId`,`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wst_cash_draws`;
CREATE TABLE `wst_cash_draws` (
  `cashId` int(11) NOT NULL AUTO_INCREMENT,
  `cashNo` varchar(50) NOT NULL,
  `targetType` tinyint(4) NOT NULL DEFAULT '0',
  `targetId` int(11) NOT NULL DEFAULT '0',
  `money` decimal(11,2) NOT NULL DEFAULT '0.00',
  `accType` tinyint(4) NOT NULL DEFAULT '0',
  `accTargetName` varchar(100) DEFAULT NULL,
  `accAreaName` varchar(100) DEFAULT NULL,
  `accNo` varchar(100) NOT NULL,
  `accUser` varchar(100) DEFAULT NULL,
  `cashSatus` tinyint(4) NOT NULL DEFAULT '0',
  `cashRemarks` varchar(255) DEFAULT NULL,
  `cashConfigId` int(11) NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`cashId`),
  KEY `targetType` (`targetType`,`targetId`),
  KEY `cashNo` (`cashNo`)
) ENGINE=InnoDB AUTO_INCREMENT=10000000 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `wst_log_moneys`;
CREATE TABLE `wst_log_moneys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetType` tinyint(4) NOT NULL DEFAULT '0',
  `targetId` int(11) NOT NULL DEFAULT '0',
  `dataId` int(11) NOT NULL DEFAULT '0',
  `dataSrc` int(11) NOT NULL DEFAULT '0',
  `remark` text NOT NULL,
  `moneyType` tinyint(4) NOT NULL DEFAULT '1',
  `money` decimal(11,2) NOT NULL DEFAULT '0.00',
  `tradeNo` varchar(100) DEFAULT NULL,
  `payType` tinyint(4) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `wst_settlements`;
CREATE TABLE `wst_settlements` (
  `settlementId` int(11) NOT NULL AUTO_INCREMENT,
  `settlementNo` varchar(20) NOT NULL,
  `settlementType` tinyint(4) NOT NULL DEFAULT '0',
  `shopId` int(11) NOT NULL,
  `accName` varchar(100) NOT NULL,
  `accNo` varchar(50) NOT NULL,
  `accUser` varchar(100) NOT NULL,
  `areaName` varchar(100) NOT NULL,
  `settlementMoney` decimal(11,2) NOT NULL DEFAULT '0.00',
  `commissionFee` decimal(11,2) NOT NULL DEFAULT '0.00',
  `backMoney` decimal(11,2) NOT NULL DEFAULT '0.00',
  `settlementStatus` tinyint(4) NOT NULL DEFAULT '0',
  `settlementTime` datetime DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`settlementId`),
  KEY `shopId` (`shopId`),
  KEY `settlementStatus` (`settlementStatus`)
) ENGINE=InnoDB AUTO_INCREMENT=10000000 DEFAULT CHARSET=utf8;


update wst_home_menus set `menuName`='资金管理' where `menuId`=43;
INSERT INTO `wst_home_menus` VALUES ('57', '0', '资金管理', 'home/shops/index', '', '1', '1', '1', '1', '2016-11-08 14:33:14');
INSERT INTO `wst_home_menus` VALUES ('58', '57', '资金管理', '#', '', '1', '1', '0', '1', '2016-11-08 15:14:33');
INSERT INTO `wst_home_menus` VALUES ('59', '58', '订单结算', 'home/settlements/index', 'home/settlements/pageQuery,home/settlements/pageUnSettledQuery,home/settlements/pageSettledQuery,home/settlements/settlement', '1', '1', '0', '1', '2016-11-08 15:34:38');
INSERT INTO `wst_home_menus` VALUES ('60', '11', '资金流水', 'home/logmoneys/usermoneys', 'home/logmoneys/pageUserQuery', '0', '1', '1', '1', '2016-11-09 23:53:50');
INSERT INTO `wst_home_menus` VALUES ('61', '58', '资金流水', 'home/logmoneys/shopmoneys', 'home/logmoneys/pageShopQuery', '1', '1', '3', '1', '2016-11-11 10:41:02');
INSERT INTO `wst_home_menus` VALUES ('62', '11', '提现管理', 'home/cashdraws/index', 'home/cashdraws/pageQuery,home/cashdraws/toEdit,home/cashdraws/drawMoney,home/cashconfigs/pageQuery,home/cashconfigs/toEdit,home/cashconfigs/add,home/cashconfigs/edit,home/cashconfigs/del', '0', '1', '5', '1', '2016-11-13 15:38:46');
INSERT INTO `wst_menus` VALUES ('62', '56', '财务管理', '0', '1');
INSERT INTO `wst_menus` VALUES ('63', '62', '提现申请', '0', '1');
INSERT INTO `wst_menus` VALUES ('64', '62', '结算管理', '2', '1');
INSERT INTO `wst_menus` VALUES ('65', '62', '商家结算', '4', '1');

INSERT INTO `wst_privileges` VALUES('175', '62', 'CWGL_00', '查看财务管理', '0', '', '', '1'),
('176', '63', 'TXSQ_00', '查看提现申请', '1', 'admin/cashdraws/index', 'admin/cashdraws/pageQuery', '1'),
('177', '63', 'TXSQ_04', '处理提现申请', '0', 'admin/cashdraws/handle', 'admin/cashdraws/toHandle', '1'),
('178', '64', 'JSSQ_00', '查看结算申请', '1', 'admin/settlements/index', 'admin/settlements/pageQuery,admin/settlements/toView,admin/settlements/pageGoodsQuery', '1'),
('179', '64', 'JSSQ_04', '处理结算申请', '0', 'admin/settlements/handle', 'admin/settlements/toHandle', '1'),
('180', '65', 'SJJS_00', '查看商家结算', '1', 'admin/settlements/toShopIndex', 'admin/settlements/pageShopQuery,admin/settlements/pageShopOrderQuery,admin/settlements/toOrders', '1'),
('181', '65', 'SJJS_04', '生成结算单', '0', 'admin/settlements/generateSettleByShop', '', '1');

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

INSERT INTO `wst_data_cats` VALUES ('4', '申请退款原因');
INSERT INTO `wst_datas` VALUES ('25', '4', '配送超时', '1', '0');
INSERT INTO `wst_datas` VALUES ('26', '4', '不喜欢/不想要', '2', '0');
INSERT INTO `wst_datas` VALUES ('27', '4', '货物破损已拒签', '3', '0');
INSERT INTO `wst_datas` VALUES ('28', '4', '空包裹', '4', '0');
INSERT INTO `wst_datas` VALUES ('29', '4', '快递/物流一直未送达', '5', '0');
INSERT INTO `wst_datas` VALUES ('30', '4', '快递/物流无跟踪记录', '6', '0');
INSERT INTO `wst_datas` VALUES ('31', '4', '其他', '10000', '0');

update wst_sys_configs set fieldValue='1.1.0_161115' where fieldCode='wstVersion';
update wst_sys_configs set fieldValue='f6dc301a2f7a1f0e6f4e4ce484acdecd' where fieldCode='wstMd5';

update wst_privileges set privilegeUrl='admin/orderrefunds/refund', otherPrivilegeUrl='admin/orderrefunds/refundPageQuery,admin/orders/view' where privilegeCode='TKDD_00'