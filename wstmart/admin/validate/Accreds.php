<?php 
namespace wstmart\admin\validate;
use think\Validate;
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
 * 认证商家验证器
 */
class Accreds extends Validate{
	protected $rule = [
	    ['accredName'  ,'require|max:30','请输入认证名称|认证名称不能超过30个字符'],
        ['accredImg'  ,'require','请上传图标']
    ];

    protected $scene = [
        'add'   =>  ['accredName','accredImg'],
        'edit'  =>  ['accredName'],
    ]; 
}