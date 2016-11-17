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
 * 店铺验证器
 */
class Shops extends Validate{
	protected $rule = [
	    ['shopSn','checkShopSn:1','请输入店铺编号|店铺编号不能超过20个字符'],
	    ['shopName'  ,'require|max:40','请输入店铺名称|店铺名称不能超过20个字符'],
        ['shopkeeper'  ,'require|max:100','请输入店主姓名|店主姓名不能超过50个字符'],
        ['telephone'  ,'require|max:40','请输入店主联系手机|店主联系手机不能超过20个字符'],
        ['shopCompany'  ,'require|max:100','请输入公司名称|公司名称不能超过50个字符'],
        ['shopTel'  ,'require|max:40','请输入店铺联系电话|店铺联系电话不能超过20个字符'],
        ['isSelf'  ,'in:0,1','无效的自营店类型'],
        ['shopImg'  ,'require','请上传店铺图标'],
        ['areaId'  ,'require','请选择店铺所在区域'],
        ['shopAddress'  ,'require','请输入店铺详细地址'],
        ['isInvoice'  ,'in:0,1','无效的发票类型'],
        ['invoiceRemarks','checkInvoiceRemark:1','请输入发票说明'],
        ['shopAtive'  ,'in:0,1','无效的营业状态'],
        ['bankId'  ,'require','请选择结算银行'],
        ['bankAreaId'  ,'require','请选择开户所地区'],
        ['bankNo'  ,'require','请选择银行账号'],
        ['bankUserName'  ,'require|max:100','请输入持卡人名称|持卡人名称长度不能能超过50个字符'],
        ['shopStatus'  ,'in:-1,1','无效的店铺状态'],
        ['statusDesc'  ,'checkStatusDesc:1','请输入店铺停止原因']
    ];

    protected $scene = [
        'add'   =>  ['shopName','shopkeeper','telephone','shopCompany','shopTel','isSelf','shopImg',
                     'areaId','shopAddress','isInvoice','shopAtive','bankId','bankAreaId','bankNo','bankUserName','shopAtive'],
        'edit'  =>  ['shopName','shopkeeper','telephone','shopCompany','shopTel','isSelf','shopImg',
                     'areaId','shopAddress','isInvoice','shopAtive','bankId','bankAreaId','bankNo','bankUserName','shopAtive']
    ]; 
    
    protected function checkShopSn($value){
    	$shopId = Input('post.shopId/d',0);
    	$key = Input('post.shopSn');
    	if($shopId>0){
    		if($key=='')return '请输入店铺编号';
    		$isChk = model('Shops')->checkShopSn($key,$shopId);
    		if($isChk)return '对不起，该店铺编号已存在';
    	}
    	return true;
    }
    
    protected function checkInvoiceRemark($value){
    	$isInvoice = Input('post.isInvoice/d',0);
    	$key = Input('post.invoiceRemarks');
    	return ($isInvoice==1 && $key=='')?'请输入发票说明':true;
    }
    
    protected function checkStatusDesc($value){
    	$shopStatus = Input('post.shopStatus/d',0);
    	$key = Input('post.statusDesc');
    	return ($shopStatus==-1 && $key=='')?'请输入店铺停止原因':true;
    }
}