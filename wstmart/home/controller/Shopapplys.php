<?php
namespace wstmart\home\controller;
use wstmart\common\model\ShopApplys as M;
use wstmart\common\model\LogSms;
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
 * 门店申请控制器
 */
class Shopapplys extends Base{	
	/**
	 * 判断手机或邮箱是否存在
	 */
	public function checkShopPhone(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		$rs = $m->checkShopPhone($userId);
		if($rs["status"]==1){
			return array("ok"=>"");
		}else{
			return array("error"=>$rs["msg"]);
		}
	
	}
	
	/**
	 * 获取验证码
	 */
	public function getPhoneVerifyCode(){
		$userPhone = input("post.userPhone2");
		$rs = array();
		if(!WSTIsPhone($userPhone)){
			return WSTReturn("手机号格式不正确!");
			exit();
		}
		$m = new M();
		$rs = $m->checkShopPhone($userPhone,(int)session('WST_USER.userId'));
		if($rs["status"]!=1){
			return WSTReturn("对不起，该手机号已提交过开店申请，如有疑问请与商城管理员联系!");
			exit();
		}
		
		$phoneVerify = rand(100000,999999);
		$msg = "欢迎您申请成为".WSTConf("CONF.mallName")."商家，您的注册验证码为:".$phoneVerify."，请在10分钟内输入。【".WSTConf("CONF.mallName")."】";
		$m = new LogSms();
		$rv = $m->sendSMS(0,$userPhone,$msg,'getPhoneVerifyCode',$phoneVerify);
		
		if($rv['status']==1){
			session('VerifyCode_shopPhone',$phoneVerify);
			session('VerifyCode_shopPhone_Time',time());
		}
		return $rv;
	}
	
	
	/**
	 * 提交申请
	 */
	public function apply(){
	
		$m = new M();
		$rs = $m->addApply();
		return $rs;
	
	}
	
	/**
	 * 跳到用户注册协议
	 */
	public function protocol(){
		return $this->fetch("shop_protocol");
	}
}
