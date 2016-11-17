<?php 
namespace wstmart\admin\validate;
use think\Validate;
use think\Db;
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
 * 职员验证器
 */
class Staffs extends Validate{
	protected $rule = [
	    ['loginName'  ,'require|max:20|checkLoginName:1','请输入登录账号|登录账号不能超过20个字符'],
	    ['loginPwd'  ,'require|min:6','请输入登录密码|登录密码不能少于6个字符'],
        ['staffName'  ,'require|max:60','请输入职员名称|职员名称不能超过20个字符'],
        ['workStatus','require|in:0,1','请选择工作状态|无效的工作状态值'],
        ['staffStatus','require|in:0,1','请选择账号状态|无效的账号状态值']
    ];

    protected $scene = [
        'add'   =>  ['loginName','loginPwd','staffName','workStatus','staffStatus'],
        'edit'  =>  ['staffName','workStatus','staffStatus']
    ]; 
    
    protected function checkLoginName($value){
    	$where = [];
    	$where['dataFlag'] = 1;
    	$where['loginName'] = $value;
    	$rs = Db::name('staffs')->where($where)->count();
    	return ($rs==0)?true:'该登录账号已存在';
    }
}