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
 * 记录用户的访问日志
 */
class ListenOperate 
{
    public function run(&$params){
        $urls = WSTConf('listenUrl');
        $request = request();
        $visit = strtolower($request->module()."/".$request->controller()."/".$request->action());
        if(array_key_exists($visit,$urls) && $urls[$visit]['isParent']){
        	$data = [];
        	$data['menuId'] = $urls[$visit]['menuId'];
        	$data['operateUrl'] = $_SERVER['REQUEST_URI'];
        	$data['operateDesc'] = $urls[$visit]['name'];
        	$data['content'] = !empty($_REQUEST)?json_encode($_REQUEST):'';
        	$data['operateIP'] = $request->ip();
        	model('admin/LogOperates')->add($data);
        }
    }
}