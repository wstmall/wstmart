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
 * 快递验证器
 */
class express extends Validate{
	protected $rule = [
        ['expressName'  ,'require|max:30','请输入快递名称|快递名称不能超过10个字符'],
    ];

    protected $scene = [
        'add'   =>  ['expressName'],
        'edit'  =>  ['expressName'],
    ]; 
}