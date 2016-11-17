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
 * 权限验证器
 */
class Friendlinks extends Validate{
	protected $rule = [
	    ['friendlinkName'  ,'require|max:90','请输入网站名称|网站名称不能超过30个字符'],
        ['friendlinkUrl'  ,'require','请输入网址']
    ];

    protected $scene = [
        'add'   =>  ['friendlinkName'],
        'edit'  =>  ['friendlinkName'],
    ]; 
}