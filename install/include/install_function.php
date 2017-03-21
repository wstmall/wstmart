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
 */
function env_check(&$env_items) {
	foreach($env_items as $key => $item) {
		$env_items[$key]['status'] = 1;
		if($key == 'os') {
			$env_items[$key]['current'] = PHP_OS;
		} elseif($key == 'php'){
			$env_items[$key]['current'] = PHP_VERSION;
		} elseif($key == 'attachmentupload') {
			if(@ini_get('file_uploads')){
				$env_items[$key]['current'] =  ini_get('upload_max_filesize');
			}else{
				$env_items[$key]['status'] = -1;
				$env_items[$key]['current'] = '没有开启文件上传';
			}
		} elseif($key == 'gdversion') {
			if(extension_loaded('gd')){
				$tmp = gd_info();
			    $env_items[$key]['current'] = empty($tmp['GD Version']) ? '' : $tmp['GD Version'];
			    unset($tmp);
			}else{
				$env_items[$key]['current'] = "没有开启GD扩展";
				$env_items[$key]['status'] = -1;
			}
		} elseif($key == 'diskspace') {
			if(function_exists('disk_free_space')) {
				$env_items[$key]['current'] = floor(disk_free_space(INSTALL_ROOT) / (1024*1024)).'M';
			} else {
				$env_items[$key]['current'] = '未知的磁盘空间';
				$env_items[$key]['status'] = 0;
			}
		}
	}
	return $env_items;
}

function dir_check(&$dir_items) {
	foreach($dir_items as $key => $item) {
		$item_path = $item['path'];
		if(!dir_writeable(INSTALL_ROOT.$item_path)) {
			if(!is_dir(INSTALL_ROOT.$item_path)) {
				$dir_items[$key]['status'] = 1;
			} else {
				$dir_items[$key]['status'] = -1;
			}
		} else {
			$dir_items[$key]['status'] = 1;
		}
	}
	return $dir_items;
}
function WSTDelDir($dirpath){
	$dh=opendir($dirpath);
	while (($file=readdir($dh))!==false) {
		if($file!="." && $file!="..") {
		    $fullpath=$dirpath."/".$file;
		    if(!is_dir($fullpath)) {
		        unlink($fullpath);
		    } else {
		        WSTDelDir($fullpath);
		        @rmdir($fullpath);
		    }
	    }
	}	 
	closedir($dh);
    $isEmpty = true;
	$dh=opendir($dirpath);
	while (($file=readdir($dh))!== false) {
		if($file!="." && $file!="..") {
			$isEmpty = false;
			break;
		}
	}
	return $isEmpty;
}
function dir_writeable($dir) {
	$writeable = 0;
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/test.txt", 'w')) {
			@fclose($fp);
			
		}
	    if(file_exists("$dir/test.txt")){
	    	$writeable = 1;
			@unlink("$dir/test.txt");
		}
	}
	return $writeable;
}
function initConfig($db_host,$db_port,$db_user,$db_pass,$db_prefix,$db_name){
	$code = "return [
			// 数据库类型
			'type'           => 'mysql',
			// 服务器地址
			'hostname'       => '".$db_host."',
			// 数据库名
			'database'       => '".$db_name."',
			// 用户名
			'username'       => '".$db_user."',
			// 密码
			'password'       => '".$db_pass."',
			// 端口
			'hostport'       => '".$db_port."',
			// 连接dsn
			'dsn'            => '',
			// 数据库连接参数
			'params'         => [],
			// 数据库编码默认采用utf8
			'charset'        => 'utf8',
			// 数据库表前缀
			'prefix'         => '".$db_prefix."',
			// 数据库调试模式
			'debug'          => false,
			// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
			'deploy'         => 0,
			// 数据库读写是否分离 主从式有效
			'rw_separate'    => false,
			// 读写分离后 主服务器数量
			'master_num'     => 1,
			// 指定从服务器序号
			'slave_no'       => '',
			// 是否严格检查字段是否存在
			'fields_strict'  => true,
			// 数据集返回类型 array 数组 collection Collection对象
			'resultset_type' => 'array',
			// 是否自动写入时间戳字段
			'auto_timestamp' => false,
			// 是否需要进行SQL性能分析
			'sql_explain'    => false,
	]";
	$code = "<?php\n ".$code.";\n?>";
    file_put_contents(INSTALL_ROOT."/wstmart/common/conf/database.php", $code);
    
    clearstatcache();
}
function check_func($func_items){
	foreach($func_items as $key => $item) {
		if(function_exists($key)){
			$func_items[$key]['current'] = '支持';
			$func_items[$key]['status'] = 1;
		}else{
			$func_items[$key]['current'] = '不支持';
			$func_items[$key]['status'] = -1;
		}
	}
	return $func_items;
}

function timezone_set($timeoffset = 8) {
	if(function_exists('date_default_timezone_set')) {
		@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
	}
}


?>