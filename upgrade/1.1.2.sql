alter table wst_privileges modify privilegeCode varchar(50);
update wst_sys_configs set fieldValue='1.1.2_161205' where fieldCode='wstVersion';
update wst_sys_configs set fieldValue='fa2fb761aaca033072f1f75912c116f0' where fieldCode='wstMd5';