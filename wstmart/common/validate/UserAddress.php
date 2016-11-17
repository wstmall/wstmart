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
 * 用户地址验证器
 */
class UserAddress extends Validate{
	protected $rule = [
        ['areaId'  ,'require','请选择地址'],
        ['userAddress'  ,'require','请输入详细地址'],
        ['userName'  ,'require','请输入联系名称'],
        ['isDefault'  ,'in:0,1','请选择是否默认地址'],
        ['userPhone'  ,'require','请输入联系电话'],
    ];

    protected $scene = [
        'add'   =>  ['areaId','userAddress','userName','isDefault','userPhone'],
        'edit'  =>  ['areaId','userAddress','userName','isDefault','userPhone'],
    ]; 
}