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
 * 属性验证器
 */
class Attributes extends Validate{
	protected $rule = [
		['attrName', 'require|max:60', '请输入属性名称|属性名称不能超过20个字符'],
		['attrType','in:0,1,2','请选择属性类型'],
		['attrVal','checkattrVal:1','请输入发票说明'],
		['isShow','in:0,1','请选择是否显示']
	];
	protected $scene = [
		'add'=>['attrName'],
		'edit'=>['attrName'],
	];
	protected function checkattrVal(){
		if(input('post.attrType/d')!=0 && input('post.attrVal')=='')return '请输入属性选项';
		return true;
	}
	
}