<?php
namespace wstmart\common\model;
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
 * 短信日志类
 */
class LogSms extends Base{

	/**
	 * 写入并发送短讯记录
	 */
	public function sendSMS($smsSrc,$phoneNumber,$content,$smsFunc,$verfyCode){
		$USER = session('WST_USER');
		$userId = empty($USER)?0:$USER['userId'];
		$ip = request()->ip();
		
		//检测短信验证码验证是否正确
		if(WSTConf("CONF.smsVerfy")==1){
			$smsverfy = input("post.smsVerfy");
			$rs = WSTVerifyCheck($smsverfy);
			if(!$rs){
				return WSTReturn("验证码不正确!",-2);
			}
		}
		//检测是否超过每日短信发送数
		$date = date('Y-m-d');
		$smsRs = $this->field("count(smsId) counts,max(createTime) createTime")
			 		  ->where(["smsPhoneNumber"=>$phoneNumber])
		 	          ->whereTime('createTime', 'between', [$date.' 00:00:00', $date.' 23:59:59'])->find();
		if($smsRs['counts']>(int)WSTConf("CONF.smsLimit")){
			return WSTReturn("请勿频繁发送短信验证!");
		}
		if($smsRs['createTime'] !='' && ((time()-strtotime($smsRs['createTime']))<120)){
			return WSTReturn("请勿频繁发送短信验证!");
		}
		//检测IP是否超过发短信次数
		$ipRs = $this->field("count(smsId) counts,max(createTime) createTime")
					 ->where(["smsIP"=>$ip])
					 ->whereTime('createTime', 'between', [$date.' 00:00:00', $date.' 23:59:59'])->find();
		if($ipRs['counts']>(int)WSTConf("CONF.smsLimit")){
			return WSTReturn("请勿频繁发送短信验证!");
		}
		if($ipRs['createTime']!='' && ((time()-strtotime($ipRs['createTime']))<120)){
			return WSTReturn("请勿频繁发送短信验证!");
		}
		$code = WSTSendSMS($phoneNumber,$content);
		$data = array();
		$data['smsSrc'] = $smsSrc;
		$data['smsUserId'] = $userId;
		$data['smsPhoneNumber'] = $phoneNumber;
		$data['smsContent'] = $content;
		$data['smsReturnCode'] = $code;
		$data['smsCode'] = $verfyCode;
		$data['smsIP'] = $ip;
		$data['smsFunc'] = $smsFunc;
		$data['createTime'] = date('Y-m-d H:i:s');
		$this->data($data)->save();
		if(intval($code)>0){
			return WSTReturn("短信发送成功!",1);
		}else{
			return WSTReturn("短信发送失败!");
		}
	}
}
