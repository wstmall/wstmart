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
 * 会员验证器
 */
class Users extends Validate{
	protected $rule = [
        ['loginName'  ,'require|max:30|checkLoginName:1','请输入账号|账号不能超过10个字符'],
    ];

    protected $scene = [
        'add'   =>  ['loginName'],
    ]; 

    protected function checkLoginName($value){
    	$where = [];
    	$where['dataFlag'] = 1;
    	$where['loginName'] = $value;
        if((int)input('userId')>0){
            $where['userId'] = ['<>',(int)input('post.userId')];
        }
    	$rs = Db::name('users')->where($where)->count();
    	return ($rs==0)?true:'该登录账号已存在';
    }

}