<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');
//进入安装目录
if(is_dir("install") && !file_exists("install/install.ok")){
	header("Location:install/index.php");
	exit();
}
// [ 应用入口文件 ]
// 定义应用目录
define('APP_PATH', __DIR__ . '/wstmart/');
define('CONF_PATH', __DIR__.'/wstmart/common/conf/');
define('WST_COMM', __DIR__.'/wstmart/common/common/');
define('WST_HOME_COMM', __DIR__.'/wstmart/home/common/');
define('WST_ADMIN_COMM', __DIR__.'/wstmart/admin/common/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
