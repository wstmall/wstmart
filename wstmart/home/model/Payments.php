<?php
namespace wstmart\home\model;
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
 * 支付管理业务处理
 */
class Payments extends Base{
	/**
	 * 获取支付方式种类
	 */
	public function getByGroup(){
		$payments = ['0'=>[],'1'=>[]];
		$rs = $this->where(['enabled'=>1])->order('payOrder asc')->select();
		foreach ($rs as $key =>$v){
			if($v['payConfig']!='')$v['payConfig'] = json_decode($v['payConfig'], true);
			$payments[$v['isOnline']][] = $v;
		}
		return $payments;
	}
	

	
	
	
	/**
	 * 获取支付信息
	 * @return unknown
	 */
	public function getPayment($payCode){
		$payment = $this->where("enabled=1 AND payCode='$payCode' AND isOnline=1")->find();
		$payConfig = json_decode($payment["payConfig"]) ;
		foreach ($payConfig as $key => $value) {
			$payment[$key] = $value;
		}
		return $payment;
	}
	
	/**
	 * 获取支付订单信息
	 */
	public function getPayOrders ($obj){
		$userId = (int)$obj["userId"];
		$orderNo = $obj["orderNo"];
		$isBatch = (int)$obj["isBatch"];
		$needPay = 0;
		$where = ["userId"=>$userId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]];
		if($isBatch==1){
			$where['orderunique'] = $orderNo;
		}else{
			$where['orderNo'] = $orderNo;
		}
		return model('orders')->where($where)->sum('needPay');
	}
	
	
	
	/**
	 * 完成支付订单
	 */
	public function complatePay ($obj){
		$trade_no = $obj["trade_no"];
		$isBatch = (int)$obj["isBatch"];
		$orderId = $obj["out_trade_no"];
		$userId = (int)$obj["userId"];
		$payFrom = (int)$obj["payFrom"];
		$payMoney = (float)$obj["total_fee"];
		
		if($payFrom>0){
			$cnt = model('orders')
						->where(['payFrom'=>$payFrom,"userId"=>$userId,"tradeNo"=>$trade_no])
						->count();
			if($cnt>0){
				return WSTReturn('订单已支付',-1);
			}
		}
		$where = ["userId"=>$userId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]];
		if($isBatch==1){
			$where['orderunique'] = $orderId;
		}else{
			$where['orderId'] = $orderId;
		}
		$needPay = model('orders')->where($where)->sum('needPay');
		
		if($needPay>$payMoney){
			return WSTReturn('支付金额不正确',-1);
		}
		Db::startTrans();
		try{
			$data = array();
			$data["needPay"] = 0;
			$data["isPay"] = 1;
			$data["orderStatus"] = 0;
			$data["tradeNo"] = $trade_no;
			$data["payFrom"] = $payFrom;
			$rs = model('orders')->where($where)->update($data);
			
			if($needPay>0 && false != $rs){
				$where = ["o.userId"=>$userId];
				if($isBatch==1){
					$where["orderunique"] = $orderId;
				}else{
					$where["orderId"] = $orderId;
				}
				$list = Db::name('orders')->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId ','inner')
					          ->where($where)->field('orderId,orderNo,s.userId')
					          ->select();
				if(!empty($list)){
					foreach ($list as $key =>$v){
						$orderId = $v["orderId"];
						//新增订单日志
						$logOrder = [];
						$logOrder['orderId'] = $orderId;
						$logOrder['orderStatus'] = 0;
						$logOrder['logContent'] = "订单已支付,下单成功";
						$logOrder['logUserId'] = $userId;
						$logOrder['logType'] = 0;
						$logOrder['logTime'] = date('Y-m-d H:i:s');
						Db::name('log_orders')->insert($logOrder);
						//创建一条充值流水记录
		            	$lm = [];
						$lm['targetType'] = 0;
						$lm['targetId'] = $userId;
						$lm['dataId'] = $orderId;
						$lm['dataSrc'] = 1;
						$lm['remark'] = '交易订单【'.$v['orderNo'].'】充值¥'.$needPay;
						$lm['moneyType'] = 1;
						$lm['money'] = $needPay;
						$lm['payType'] = $payFrom;
						$lm['tradeNo'] = $trade_no;
						$lm['createTime'] = date('Y-m-d H:i:s');
						model('LogMoneys')->save($lm);
						//创建一条支出流水记录
		            	$lm = [];
						$lm['targetType'] = 0;
						$lm['targetId'] = $userId;
						$lm['dataId'] = $orderId;
						$lm['dataSrc'] = 1;
						$lm['remark'] = '交易订单【'.$v['orderNo'].'】支出¥'.$needPay;
						$lm['moneyType'] = 0;
						$lm['money'] = $needPay;
						$lm['payType'] = 0;
						$lm['createTime'] = date('Y-m-d H:i:s');
						model('LogMoneys')->save($lm);

						//发送一条商家信息
						$msgContent = "订单【".$v['orderNo']."】用户已支付完成，请尽快发货哦~";
						WSTSendMsg($v["userId"],$msgContent,['from'=>1,'dataId'=>$orderId]);
					}
				}
			}
			Db::commit();
			return WSTReturn('支付成功',1);
		}catch (\Exception $e) {
			Db::rollback();
			return WSTReturn('操作失败',-1);
		}
	}
	
}
