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
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(0);
define('IN_WSTMART', TRUE);
define('INSTALL_ROOT', dirname(dirname(dirname(__FILE__))));
define('INSTALL_PATH', dirname(dirname(__FILE__)));
if(file_exists(INSTALL_PATH.'/install.ok'))return -1;
require 'install_var.php';
require 'install_function.php';

if(function_exists('mysqli_connect')) {
    require 'install_mysql.php';
}
$db = new MySql;
$db_host = trim($_POST['db_host']);
$db_port = (int)trim($_POST['db_port']);
if($db_port==0)$db_port = 3306;
$db_user = trim($_POST['db_user']);
$db_pass = trim($_POST['db_pass']);
$db_prefix = trim($_POST['db_prefix']);
$db_name = trim($_POST['db_name']);
$admin_name = trim($_POST['admin_name']);
$admin_password = trim($_POST['admin_password']);
$db_type = (int)trim($_POST['db_demo']);
$act = $_POST['act'];
$db->connect($db_host,$db_port, $db_user, $db_pass, $db_name, DBCHARSET,$db_prefix);
if($act=='list'){
	$list = array();
    $dh=opendir('../data/'.$db_type);
	while (($file=readdir($dh))!== false) {
		if($file!="." && $file!="..") {
			$list[] = $file;
		}
	}
	echo json_encode(array('status'=>1,'list'=>$list));
}else if($act=='insert'){
	$sql = "../data/".$db_type."/wst_".$_POST['table'].".sql";
	$sql = file_get_contents($sql);
	$db->excute($sql);
	if(intval($_POST['isFinish'])==1){
		$sql = "UPDATE ".$db_prefix."staffs SET loginName='".$admin_name."',loginPwd='".md5($admin_password."9365")."' WHERE staffId=1";
	    $db->excute($sql);
	    initConfig($db_host,$db_port,$db_user,$db_pass,$db_prefix,$db_name);
	    if(!file_exists(INSTALL_ROOT."/wstmart/common/conf/database.php")){
	    	echo json_encode(array('status'=>-1,'msg'=>'无法创建配置文件，请检查wstmart/common/conf目录是否有写入权限!'));exit();
	    }
	    $counter_file = INSTALL_PATH.'/install.ok';
		$fopen = fopen($counter_file,'wb');
		fputs($fopen,   @date('Y-m-d H:i:s'));
		fclose($fopen);
		if(file_exists(INSTALL_PATH.'/install.ok')){
			WSTDelDir(INSTALL_ROOT."/runtime");
	        echo json_encode(array('status'=>1));exit();
		}else{
			echo json_encode(array('status'=>-1,'msg'=>'无法创建配置文件，请检查install目录是否有写入权限!'));exit();
		}
    }
    echo json_encode(array('status'=>1));
}

?>