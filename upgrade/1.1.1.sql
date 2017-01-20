INSERT INTO `wst_menus` VALUES ('61', '2', '风格管理', '10', '1');
INSERT INTO `wst_privileges` VALUES ('173', '61', 'FGGL_00', '查看风格管理', '1', 'admin/styles/index', '', '1');
INSERT INTO `wst_privileges` VALUES ('174', '61', 'FGGL_04', '风格管理', '0', 'admin/styles/edit', '', '1');

INSERT INTO `wst_data_cats` VALUES ('5', '广告类型');
INSERT INTO `wst_datas` VALUES ('32', '5', 'PC版', '1', '0');

alter table `wst_styles` modify `styleSys` varchar(20);
update `wst_styles` set `styleSys`='home' where `styleSys`=0;

update wst_privileges set privilegeUrl='admin/styles/index' where privilegeCode='FGGL_00';

insert into wst_sys_configs(fieldName,fieldCode,fieldValue) value('未付款订单有效期','autoCancelNoPayDays',24);
update wst_home_menus set menuOtherUrl='home/orders/waitDeliveryByPage,home/orders/deliver,home/orders/view,home/orders/getMoneyByOrder,home/orders/orderPrint' where menuUrl='home/orders/waitdelivery';
update wst_home_menus set menuOtherUrl='home/orders/deliveredByPage,home/orders/view,home/orders/orderPrint' where menuUrl='home/orders/delivered';
update wst_home_menus set menuOtherUrl='home/orders/failureByPage,home/orders/view,home/orders/confer,home/orders/confer,home/orders/orderPrint' where menuUrl='home/orders/failure';
update wst_home_menus set menuOtherUrl='home/orders/finishedByPage,home/orders/view,home/orders/orderPrint',menuType=1 where menuUrl='home/orders/finished';

update wst_sys_configs set fieldValue='1.1.1_161121' where fieldCode='wstVersion';
update wst_sys_configs set fieldValue='0e1f60a752c131a07e462cac43fab9ab' where fieldCode='wstMd5';
