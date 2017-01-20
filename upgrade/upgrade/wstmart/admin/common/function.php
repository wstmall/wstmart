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
/**
 * 加载系统访问路径
 */
function WSTVisitPrivilege(){
	 $listenUrl = cache('WST_LISTEN_URL');
	 if(!$listenUrl){
	     $list = model('admin/Privileges')->getAllPrivileges();
	     $listenUrl = [];
	     foreach ($list as $v){
	     	if($v['privilegeUrl']=='')continue;
	        $listenUrl[strtolower($v['privilegeUrl'])] = ['code'=>$v['privilegeCode'],
												          'url'=>$v['privilegeUrl'],
												          'name'=>$v['privilegeName'],
												          'isParent'=>true,
	        			                                  'menuId'=>$v['menuId']
	                                                     ];
	        if(strpos($v['otherPrivilegeUrl'],'/')!==false){
	        	$t = explode(',',$v['otherPrivilegeUrl']);
	        	foreach ($t as $vv){
	        		if(strpos($vv,'/')!==false){
	        			$listenUrl[strtolower($vv)] = ['code'=>$v['privilegeCode'],
									        		   'url'=>$vv,
									        		   'name'=>$v['privilegeName'],
									        		   'isParent'=>false,
	        			                               'menuId'=>$v['menuId']
									        		  ];
	        		}
	        	}
	        }
	     }
	     cache('WST_LISTEN_URL',$listenUrl);
	 }
     return $listenUrl;
}

/**
 * 判断有没有权限
 * @param $code 权限代码
 * @param $type 返回的类型  true-boolean   false-string
 */
function WSTGrant($code){
	$STAFF = session("WST_STAFF");
	if(in_array($code,$STAFF['privileges']))return true;
	return false;
}

/**
 * 循环删除指定目录下的文件及文件夹
 * @param string $dirpath 文件夹路径
 */
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

