<?php
namespace wstmart\admin\model;
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
 * 退款订单业务处理类
 */
class OrderRefunds extends Base{
	
    /**
	 * 获取用户退款订单列表
	 */
	public function refundPageQuery(){
		$where = ['o.dataFlag'=>1];
		$where['orderStatus'] = ['in',[-1,-3]];
		$where['o.payType'] = 1;
		$orderNo = input('orderNo');
		$shopName = input('shopName');
		$deliverType = (int)input('deliverType',-1);
		$areaId1 = (int)input('areaId1');
		if($areaId1>0){
			$where['s.areaIdPath'] = ['like',"$areaId1%"];
			$areaId2 = (int)input("areaId1_".$areaId1);
			if($areaId2>0)$where['s.areaIdPath'] = ['like',$areaId1."_"."$areaId2%"];
			$areaId3 = (int)input("areaId1_".$areaId1."_".$areaId2);
			if($areaId3>0)$where['s.areaId'] = $areaId3;
		}
		$isRefund = (int)input('isRefund',-1);
		if($orderNo!='')$where['orderNo'] = ['like','%'.$orderNo.'%'];
		if($shopName!='')$where['shopName|shopSn'] = ['like','%'.$shopName.'%'];
		
		if($deliverType!=-1)$where['o.deliverType'] = $deliverType;
		if($isRefund!=-1)$where['o.isRefund'] = $isRefund;
		$page = Db::name('orders')->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		     ->join('__USERS__ u','o.userId=u.userId','left')
		     ->join('__ORDER_REFUNDS__ orf ','o.orderId=orf.orderId and refundStatus=1') 
		     ->where($where)
		     ->field('orf.id refundId,o.orderId,o.orderNo,s.shopName,s.shopId,s.shopQQ,s.shopWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,u.loginName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,orf.backMoney,orf.refundRemark,isRefund,orf.createTime')
			 ->order('orf.createTime', 'desc')
			 ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['Rows'])>0){
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['payType'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	/**
	 * 获取退款资料
	 */
	public function getInfoByRefund(){
		return $this->alias('orf')->join('__ORDERS__ o','orf.orderId=o.orderId')->where(['orf.id'=>(int)input('get.id'),'payType'=>1,'isRefund'=>0,'orderStatus'=>['in',[-1,-3]],'refundStatus'=>1])
		         ->field('orf.id refundId,orderNo,o.orderId,goodsMoney,refundReson,refundOtherReson,totalMoney,realTotalMoney,deliverMoney,payType,payFrom,backMoney,tradeNo')
		         ->find();
	}
	/**
	 * 退款
	 */
	public function orderRefund(){
		$id = (int)input('post.id');
		$content = input('post.content');
		if($id==0)return WSTReturn("操作失败!");
		$refund = $this->get($id);
		if(empty($refund) || $refund->refundStatus!=1)return WSTReturn("该退款订单不存在或已退款!");
		Db::startTrans();
        try{
        	$order = model('orders')->get($refund->orderId);
        	if(!in_array($order->orderStatus,[-1,-3]) || $order->payType !=1)return WSTReturn("无效的退款订单!");
			//修改退款单信息
			$refund->refundRemark = $content;
			$refund->refundTime = date('Y-m-d H:i:s');
			$refund->refundStatus = 2;
			$refund->save();
			//修改订单状态
			$order->isRefund = 1;
			$order->save();
			//修改用户账户金额
			Db::name('users')->where('userId',$order->userId)->setInc('userMoney',$refund->backMoney);		
			//创建用户资金流水记录
			$lm = [];
			$lm['targetType'] = 0;
			$lm['targetId'] = $order->userId;
			$lm['dataId'] = $order->orderId;
			$lm['dataSrc'] = 1;
			$lm['remark'] = '订单【'.$order->orderNo.'】退款¥'.$refund->backMoney."。".(($content!='')?"【退款备注】：".$content:'');
			$lm['moneyType'] = 1;
			$lm['money'] = $refund->backMoney;
			$lm['payType'] = 0;
			$lm['createTime'] = date('Y-m-d H:i:s');
			Db::name('log_moneys')->insert($lm);
			//发送一条用户信息
			WSTSendMsg($order->userId,"您的退款订单【".$order->orderNo."】已处理，请留意账户到账情况。".(($content!='')?"【退款备注：".$content."】":""),['from'=>1,'dataId'=>$order->orderId]);
			//如果有钱剩下，那么就退回到商家钱包
			$shopMoneys = $order->realTotalMoney-$refund->backMoney;
			if($shopMoneys>0){
				$shop = model('shops')->get($order->shopId);
				$shop->shopMoney = $shop->shopMoney + $shopMoneys;
				$shop->save();
                //创建商家资金流水
                $lm = [];
				$lm['targetType'] = 1;
				$lm['targetId'] = $order->shopId;
				$lm['dataId'] = $order->orderId;
				$lm['dataSrc'] = 1;
				$lm['remark'] = '订单【'.$order->orderNo.'】退款，返回商家金额¥'.$shopMoneys."。";
				$lm['moneyType'] = 1;
				$lm['money'] = $shopMoneys;
				$lm['payType'] = 0;
				$lm['createTime'] = date('Y-m-d H:i:s');
				Db::name('log_moneys')->insert($lm);
				//发送商家信息
				WSTSendMsg($shop->userId,"退款订单【".$order->orderNo."】已通过，返回商家金额¥".$shopMoneys."，请留意账户情况。",['from'=>1,'dataId'=>$order->orderId]);
			}
			Db::commit();
			return WSTReturn("操作成功",1); 
        }catch (\Exception $e) {
            Db::rollback();
        }
		return WSTReturn("操作失败，请刷新后再重试"); 
	}
}
