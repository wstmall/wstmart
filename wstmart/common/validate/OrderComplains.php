<?php 
namespace wstmart\common\validate;
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
 * 订单投诉验证器
 */
class OrderComplains extends Validate{
	protected $rule = [
        ['complainType'  ,'in:1,2,3,4','无效的投诉类型！'],
        ['complainContent'  ,'require|length:3,600','投诉内容不能为空|投诉内容应为3-200个字'],
        ['respondContent'  ,'require|length:3,600','应诉内容不能为空|应诉内容应为3-200个字'],
    ];

    protected $scene = [
        'add'   =>  ['complainType','complainContent'],
        'edit'   =>  ['complainType','complainContent'],
        'respond' =>['respondContent'],
    ]; 
}