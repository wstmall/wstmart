<?php
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtaosoft.com
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 广告控制器
 */
if(!defined('IN_WSTMART')) {
	exit('Access Denied');
}
define('CHARSET', 'utf-8');
define('DBCHARSET', 'utf8');
define('TABLEPRE', 'wst_');

$env_items = array
(
	'os' => array(''),
	'php' => array(''),
	'attachmentupload' => array(),
	'gdversion' => array(),
	'diskspace' => array(),
);
$dir_items = array
(
  'install' => array('path' => '/install'),
  'runtime' => array('path' => '/runtime'),
  'upload' => array('path' => '/upload'),
  'conf' => array('path' => '/wstmart/common/conf')
);
$func_items = array(
  'mysqli_connect'=>array(),
  'file_get_contents'=>array(),
  'curl_init'=>array(),
  'mb_strlen'=>array(),
  'finfo_open'=>array()
);
?>