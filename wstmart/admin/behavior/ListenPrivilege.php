<?php
namespace wstmart\admin\behavior;
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
 * 检测有没有访问权限
 */
class ListenPrivilege 
{
    public function run(&$params){
        $privileges = session('WST_STAFF.privileges');
        $urls = WSTConf('listenUrl');
        $request = request();
        $visit = strtolower($request->module()."/".$request->controller()."/".$request->action());
        if(array_key_exists($visit,$urls) && !in_array($urls[$visit]['code'],$privileges)){
        	if($request->isAjax()){
        		echo json_encode(['status'=>-998,'msg'=>'对不起，您没有操作权限，请与管理员联系']);
        	}else{
        		header("Content-type: text/html; charset=utf-8");
        	    echo "对不起，您没有操作权限，请与管理员联系";
        	}
        	exit();
        }
    }
}