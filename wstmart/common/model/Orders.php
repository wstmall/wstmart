<?php
namespace wstmart\common\model;
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
 * 订单业务处理类
 */
class Orders extends Base{
	/**
	 * 提交订单
	 */
	public function submit(){
		$addressId = (int)input('post.s_addressId');
		$deliverType = ((int)input('post.deliverType')!=0)?1:0;
		$isInvoice = ((int)input('post.isInvoice')!=0)?1:0;
		$invoiceClient = ($isInvoice==1)?input('post.invoiceClient'):'';
		$payType = ((int)input('post.payType')!=0)?1:0;
		$userId = (int)session('WST_USER.userId');
		//检测购物车
		$carts = model('carts')->getCarts(true);
		if(empty($carts['carts']))return WSTReturn("请选择要购买的商品");
		//检测地址是否有效
		$address = Db::name('user_address')->where(['userId'=>$userId,'addressId'=>$addressId,'dataFlag'=>1])->find();
		if(empty($address)){
			return WSTReturn("无效的用户地址");
		}
	    $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$address['areaIdPath']);
        $address['areaId2'] = $tmp[1];//记录配送城市
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::name('areas')->where(['dataFlag'=>1,'areaId'=>['in',$areaIds]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$address['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $address['areaName'] = implode('',$areaNames);
	         }
         }
		$address['userAddress'] = $address['areaName'].$address['userAddress'];
		WSTUnset($address, 'isDefault,dataFlag,createTime,userId');
		//生成订单
		Db::startTrans();
		try{
			$orderunique = WSTOrderQnique();
			foreach ($carts['carts'] as $ckey =>$shopOrder){
				$orderNo = WSTOrderNo(); 
				$orderScore = 0;
				//创建订单
				$order = [];
				$order = array_merge($order,$address);
				$order['orderNo'] = $orderNo;
				$order['userId'] = $userId;
				$order['shopId'] = $shopOrder['shopId'];
				$order['payType'] = $payType;
				if($payType==1){
				    $order['orderStatus'] = -2;//待付款
				    $order['isPay'] = 0;
				}else{
					$order['orderStatus'] = 0;//待发货
				}
				$order['goodsMoney'] = $shopOrder['goodsMoney'];
				$order['deliverType'] = $deliverType;
				$order['deliverMoney'] = ($deliverType==1)?0:WSTOrderFreight($shopOrder['shopId'],$order['areaId2']);
				$order['totalMoney'] = $order['goodsMoney']+$order['deliverMoney'];
				$order['realTotalMoney'] = $order['totalMoney'];
				$order['needPay'] = $order['realTotalMoney'];
				//积分
				$orderScore = 0;
				//如果开启下单获取积分则有积分
				if(WSTConf('CONF.isOrderScore')==1){
				    $orderScore = round($order['goodsMoney'],0);
				}
				$order['orderScore'] = $orderScore;
				$order['isInvoice'] = $isInvoice;
				$order['invoiceClient'] = $invoiceClient;
				$order['orderRemarks'] = input('post.remark_'.$shopOrder['shopId']);
				$order['orderunique'] = $orderunique;
				$order['orderSrc'] = 0;
				$order['dataFlag'] = 1;
				$order['createTime'] = date('Y-m-d H:i:s');
				$result = $this->data($order,true)->isUpdate(false)->allowField(true)->save($order);
				if(false !== $result){
					$orderId = $this->orderId;
					$orderTotalGoods = [];
					foreach ($shopOrder['list'] as $gkey =>$goods){
						//创建订单商品记录
						$orderGgoods = [];
						$orderGoods['orderId'] = $orderId;
						$orderGoods['goodsId'] = $goods['goodsId'];
						$orderGoods['goodsNum'] = $goods['cartNum'];
						$orderGoods['goodsPrice'] = $goods['shopPrice'];
						$orderGoods['goodsSpecId'] = $goods['goodsSpecId'];
						if(!empty($goods['specNames'])){
							$specNams = [];
							foreach ($goods['specNames'] as $pkey =>$spec){
								$specNams[] = $spec['catName'].'：'.$spec['itemName'];
							}
							$orderGoods['goodsSpecNames'] = implode('@@_@@',$specNams);
						}else{
							$orderGoods['goodsSpecNames'] = '';
						}
						$orderGoods['goodsName'] = $goods['goodsName'];
						$orderGoods['goodsImg'] = $goods['goodsImg'];
						$orderGoods['commissionRate'] = WSTGoodsCommissionRate($goods['goodsCatId']);
						$orderTotalGoods[] = $orderGoods;
						//修改库存
						if($goods['goodsSpecId']>0){
					        Db::name('goods_specs')->where('id',$goods['goodsSpecId'])->setDec('specStock',$goods['cartNum']);
						}
						Db::name('goods')->where('goodsId',$goods['goodsId'])->setDec('goodsStock',$goods['cartNum']);
					}
					Db::name('order_goods')->insertAll($orderTotalGoods);
					//建立订单记录
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = $order['orderStatus'];
					$logOrder['logContent'] = ($payType==1)?"下单成功，等待用户支付":"下单成功";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//给店铺增加提示消息
					WSTSendMsg($shopOrder['userId'],"您有一笔新的订单【".$orderNo."】待处理。",['from'=>1,'dataId'=>$orderId]);
				}
			}
			//删除已选的购物车商品
			Db::name('carts')->where(['userId'=>$userId,'isCheck'=>1])->delete();
			Db::commit();
			return WSTReturn("提交订单成功", 1,$orderunique);
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('提交订单失败',-1);
        }
	}
	
	/**
	 * 根据订单唯一流水获取订单信息
	 */
	public function getByUnique(){
		$id = input('id');
		$isBatch = input('isBatch/d',1);
		$userId = (int)session('WST_USER.userId');
		if($isBatch==1){
			$rs = $this->where(['userId'=>$userId,'orderunique'=>$id])->field('orderId,orderNo,payType,needPay,orderunique,deliverMoney')->select();
		}else{
			$rs = $this->where(['userId'=>$userId,'orderId'=>$id])->field('orderId,orderNo,payType,needPay,orderunique,deliverMoney')->select();
		}
		
		$data = [];
		$data['orderunique'] = $id;
		$data['list'] = [];
		$payType = 0;
		$totalMoney = 0;
		$orderIds = [];
		foreach ($rs as $key =>$v){
			if($v['payType']==1)$payType = 1;
			$totalMoney = $totalMoney + $v['needPay'];
			$orderIds[] = $v['orderId'];
			$data['list'][] = $v;
		}
		$data['totalMoney'] = $totalMoney;
		$data['payType'] = $payType;
		//如果是在线支付的话就要加载商品信息和支付信息
		if($data['payType']==1){
			//获取商品信息
			$goods = Db::name('order_goods')->where(['orderId'=>['in',$orderIds]])->select();
			foreach ($goods as $key =>$v){
				if($v['goodsSpecNames']!=''){
				    $v['goodsSpecNames'] = explode('@@_@@',$v['goodsSpecNames']);
				}else{
					$v['goodsSpecNames'] = [];
				}
				$data['goods'][$v['orderId']][] = $v;
			}
			//获取支付信息
			$payments = model('payments')->where(['isOnline'=>1,'enabled'=>1])->order('payOrder asc')->select();
			$data['payments'] = $payments;
		}
		return $data;
	}
	
	/**
	 * 获取用户订单列表
	 */
	public function userOrdersByPage($orderStatus,$isAppraise = -1){
		$userId = (int)session('WST_USER.userId');
		$orderNo = input('post.orderNo');
		$shopName = input('post.shopName');
		$isRefund = (int)input('post.isRefund',-1);
		$where = ['o.userId'=>$userId,'o.dataFlag'=>1];
		if(is_array($orderStatus)){
			$where['orderStatus'] = ['in',$orderStatus];
		}else{
			$where['orderStatus'] = $orderStatus;
		}
		if($isAppraise!=-1)$where['isAppraise'] = $isAppraise;
		if($orderNo!=''){
			$where['o.orderNo'] = ['like',"%$orderNo%"];
		}
		if($shopName != ''){
			$where['s.shopName'] = ['like',"%$shopName%"];
		}
		if(in_array($isRefund,[0,1])){
			$where['isRefund'] = $isRefund;
		}

		$page = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		             ->join('__ORDER_COMPLAINS__ oc','oc.orderId=o.orderId','left')
		             ->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and orf.refundStatus!=-1','left')
		             ->where($where)
		             ->field('o.orderId,o.orderNo,s.shopName,s.shopId,s.shopQQ,s.shopWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.deliverType,deliverMoney,isPay,payType,payFrom,o.orderStatus,needPay,isAppraise,isRefund,orderSrc,o.createTime,oc.complainId,orf.id refundId')
			         ->order('o.createTime', 'desc')
			         ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['Rows'])>0){
	    	 $orderIds = [];
	    	 foreach ($page['Rows'] as $v){
	    	 	 $orderIds[] = $v['orderId'];
	    	 }
	    	 $goods = Db::name('order_goods')->where('orderId','in',$orderIds)->select();
	    	 $goodsMap = [];
	    	 foreach ($goods as $v){
	    	 	 $v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
	    	 	 $goodsMap[$v['orderId']][] = $v;
	    	 }
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['list'] = $goodsMap[$v['orderId']];
	    	 	 $page['Rows'][$key]['isComplain'] = 1;
	    	 	 if(($v['complainId']=='') && ($v['payType']==0 || ($v['payType']==1 && $v['orderStatus']!=2))){
	    	 	 	$page['Rows'][$key]['isComplain'] = '';
	    	 	 }
	    	 	 $page['Rows'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	
	/**
	 * 获取商家订单
	 */
	public function shopOrdersByPage($orderStatus){
		$orderNo = input('post.orderNo');
		$shopName = input('post.shopName');
		$payType = (int)input('post.payType');
		$deliverType = (int)input('post.deliverType');

		$shopId = (int)session('WST_USER.shopId');
		$where = ['shopId'=>$shopId,'dataFlag'=>1];
		if(is_array($orderStatus)){
			$where['orderStatus'] = ['in',$orderStatus];
		}else{
			$where['orderStatus'] = $orderStatus;
		}
		if($orderNo!=''){
			$where['orderNo'] = ['like',"%$orderNo%"];
		}
		if($shopName!=''){
			$where['shopName'] = ['like',"%$shopName%"];
		}
		if($payType > -1){
			$where['payType'] =  $payType;
		}
		if($deliverType > -1){
			$where['deliverType'] =  $deliverType;
		}
		$page = $this->alias('o')->where($where)
		      ->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and refundStatus=0','left')
		      ->field('o.orderId,orderNo,goodsMoney,totalMoney,realTotalMoney,orderStatus,deliverType,deliverMoney,isAppraise
		              ,payType,payFrom,userAddress,orderStatus,isPay,isAppraise,userName,orderSrc,o.createTime,orf.id refundId')
			  ->order('o.createTime', 'desc')
			  ->paginate()->toArray();
	    if(count($page['Rows'])>0){
	    	 $orderIds = [];
	    	 foreach ($page['Rows'] as $v){
	    	 	 $orderIds[] = $v['orderId'];
	    	 }
	    	 $goods = Db::name('order_goods')->where('orderId','in',$orderIds)->select();
	    	 $goodsMap = [];
	    	 foreach ($goods as $v){
	    	 	 $v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
	    	 	 $goodsMap[$v['orderId']][] = $v;
	    	 }
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['list'] = $goodsMap[$v['orderId']];
	    	 	 $page['Rows'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	/**
	 * 商家发货
	 */
	public function deliver(){
		$orderId = (int)input('post.id');
		$expressId = (int)input('post.expressId');
		$expressNo = input('post.expressNo');
		$shopId = (int)session('WST_USER.shopId');
		$userId = (int)session('WST_USER.userId');
		$order = $this->where(['shopId'=>$shopId,'orderId'=>$orderId,'orderStatus'=>0])->field('orderId,orderNo,userId')->find();
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>1,'expressId'=>$expressId,'expressNo'=>$expressNo,'deliveryTime'=>date('Y-m-d H:i:s')];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 1;
					$logOrder['logContent'] = "商家已发货".(($expressNo!='')?"，快递号为：".$expressNo:"");
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//发送一条用户信息
					$msgContent = "您的订单【".$order['orderNo']."】已发货啦".(($expressNo!='')?"，快递号为：".$expressNo:"")."，请做好收货准备哦~";
					WSTSendMsg($order['userId'],$msgContent,['from'=>1,'dataId'=>$orderId]);
					Db::commit();
					return WSTReturn('操作成功',1);
				}
			}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户收货
	 */
	public function receive(){
		$orderId = (int)input('post.id');
		$userId = (int)session('WST_USER.userId');
		$order = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		              ->where(['o.userId'=>$userId,'o.orderId'=>$orderId,'o.orderStatus'=>1])
		              ->field('o.orderId,o.orderNo,o.payType,s.userId,s.shopId,o.orderScore')->find();
		if(!empty($order)){
			Db::startTrans();
		    try{
		    	//计算订单佣金
				$goods = Db::name('order_goods')->field('goodsNum,goodsPrice,commissionRate')->where('orderId',$order['orderId'])->select();
				$commissionFee = 0;
				foreach ($goods as $key => $v) {
					if((float)$v['commissionRate']>0){
						$commissionFee += round($v['goodsPrice']*$v['goodsNum']*$v['commissionRate']/100,2);
					}
				}

				$data = ['orderStatus'=>2,'receiveTime'=>date('Y-m-d H:i:s'),'commissionFee'=>$commissionFee];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//修改商家未计算订单数
					$prefix = config('database.prefix');
					$upSql = 'update '.$prefix.'shops set noSettledOrderNum=noSettledOrderNum+1,noSettledOrderFee=noSettledOrderFee-'.$commissionFee.' where shopId='.$order['shopId'];
					Db::execute($upSql);
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 2;
					$logOrder['logContent'] = "用户已收货";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//发送一条商家信息
					WSTSendMsg($order['userId'],"您的订单【".$order['orderNo']."】，用户已签收",['from'=>1,'dataId'=>$orderId]);
					//给用户增加积分
					if(WSTConf("CONF.isOrderScore")==1){
						$score = [];
						$score['userId'] = $userId;
						$score['score'] = $order['orderScore'];
						$score['dataSrc'] = 1;
						$score['dataId'] = $orderId;
						$score['dataRemarks'] = "交易订单【".$order['orderNo']."】获得积分".$order['orderScore']."个";
						$score['scoreType'] = 1;
						$score['createTime'] = date('Y-m-d H:i:s');
						model('UserScores')->save($score);
					}
					Db::commit();
					return WSTReturn('操作成功',1);
				}
		    }catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户取消订单
	 */
	public function cancel(){
		$orderId = (int)input('post.id');
		$reason = (int)input('post.reason');
		$userId = (int)session('WST_USER.userId');
		$order = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		              ->where(['o.userId'=>$userId,'o.orderId'=>$orderId,'o.orderStatus'=>['in',[-2,0]]])
		              ->field('o.orderId,o.orderNo,s.userId')->find();
		$reasonData = WSTDatas(1,$reason);
		if(empty($reasonData))return WSTReturn("无效的取消原因");
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>-1,'cancelReason'=>$reason];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//返还商品库存
					$goods = Db::name('order_goods')->alias('og')->join('__GOODS__ g','og.goodsId=g.goodsId','inner')
					           ->where('orderId',$orderId)->field('og.*,g.isSpec')->select();
					foreach ($goods as $key => $v){
						//修改库存
						if($v['isSpec']>0){
					        Db::name('goods_specs')->where('id',$v['goodsSpecId'])->setInc('specStock',$v['goodsNum']);
						}
						Db::name('goods')->where('goodsId',$v['goodsId'])->setInc('goodsStock',$v['goodsNum']);
					}
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = -1;
					$logOrder['logContent'] = "用户取消订单，取消原因：".$reasonData['dataName'];
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//发送一条商家信息
					WSTSendMsg($order['userId'],"订单【".$order['orderNo']."】用户已取消，取消原因：".$reasonData['dataName'],['from'=>1,'dataId'=>$orderId]);
					Db::commit();
					return WSTReturn('订单取消成功',1);
				}
			}catch (\Exception $e) {
		        Db::rollback();
		        print_r($e);
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户拒收订单
	 */
	public function reject(){
		$orderId = (int)input('post.id');
		$reason = (int)input('post.reason');
		$content = input('post.content');
		$userId = (int)session('WST_USER.userId');
		$order = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		              ->where(['o.userId'=>$userId,'o.orderId'=>$orderId,'o.orderStatus'=>1])
		              ->field('o.orderId,o.orderNo,s.userId')->find();
		$reasonData = WSTDatas(2,$reason);
		if(empty($reasonData))return WSTReturn("无效的拒收原因");
		if($reason==10000 && $content=='')return WSTReturn("请输入拒收原因");
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>-3,'rejectReason'=>$reason];
				if($reason==10000)$data['rejectOtherReason'] = $content;
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = -3;
					$logOrder['logContent'] = "用户拒收订单，拒收原因：".$reasonData['dataName'].(($reason==10000)?"-".$content:"");
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//发送一条商家信息
					$msgContent = "订单【".$order['orderNo']."】用户拒收，拒收原因：".$reasonData['dataName'].(($reason==10000)?"-".$content:"");
					WSTSendMsg($order['userId'],$msgContent,['from'=>1,'dataId'=>$orderId]);
					Db::commit();
					return WSTReturn('操作成功',1);
				}
			}catch (\Exception $e) {
		        Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 获取订单价格
	 */
	public function getMoneyByOrder($orderId = 0){
		$orderId = ($orderId>0)?$orderId:(int)input('post.id');
		return $this->where('orderId',$orderId)->field('orderId,goodsMoney,deliverMoney,totalMoney,realTotalMoney')->find();
	}
	

	/**
	 * 修改订单价格
	 */
	public function editOrderMoney(){
		$orderId = input('post.id');
		$orderMoney = (float)input('post.orderMoney');
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		if($orderMoney<0)return WSTReturn("订单价格不能小于0");
		Db::startTrans();
		try{
			$result = $this->where(['orderId'=>$orderId,'shopId'=>$shopId,'orderStatus'=>-2])->update(['realTotalMoney'=>$orderMoney]);
			if(false !== $result){
				//新增订单日志
				$logOrder = [];
				$logOrder['orderId'] = $orderId;
				$logOrder['orderStatus'] = -2;
				$logOrder['logContent'] = "商家修改订单价格为：".$orderMoney;
				$logOrder['logUserId'] = $userId;
				$logOrder['logType'] = 0;
				$logOrder['logTime'] = date('Y-m-d H:i:s');
				Db::name('log_orders')->insert($logOrder);
				Db::commit();
				return WSTReturn('操作成功',1);
			}
		}catch (\Exception $e) {
		    Db::rollback();
	        return WSTReturn('操作失败',-1);
	    }
	}
	
	/**
	 * 获取订单详情
	 */
	public function getByView($orderId){
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$orders = $this->alias('o')->join('__EXPRESS__ e','o.expressId=e.expressId','left')
		               ->join('__SHOPS__ s','o.shopId=s.shopId','left')
		               ->join('__ORDER_REFUNDS__ orf ','o.orderId=orf.orderId and orf.refundStatus=2','left')
		               ->where('o.dataFlag=1 and o.orderId='.$orderId.' and ( o.userId='.$userId.' or o.shopId='.$shopId.')')
		               ->field('o.*,e.expressName,s.shopTel,s.shopName,s.shopQQ,s.shopWangWang,orf.refundRemark,orf.refundTime,orf.backMoney')->find();
		if(empty($orders))return WSTReturn("无效的订单信息");
		
		//获取订单信息
		$orders['log'] =Db::name('log_orders')->where('orderId',$orderId)->order('logId asc')->select();
		//获取订单商品
		$orders['goods'] = Db::name('order_goods')->where('orderId',$orderId)->order('id asc')->select();
		return $orders;
	}



	/**
	* 根据订单id获取 商品信息跟商品评价
	*/
	public function getOrderInfoAndAppr(){
		$orderId = (int)input('oId');
		$userId = (int)session('WST_USER.userId');

		$goodsInfo = Db::name('order_goods')
					->field('id,orderId,goodsName,goodsId,goodsSpecNames,goodsImg,goodsSpecId')
					->where(['orderId'=>$orderId])
					->select();
		//根据商品id 与 订单id 取评价
		$alreadys = 0;// 已评价商品数
		$count = count($goodsInfo);//订单下总商品数
		if($count>0){
			foreach($goodsInfo as $k=>$v){
				$goodsInfo[$k]['goodsSpecNames'] = str_replace('@@_@@', ';', $v['goodsSpecNames']);
				$appraise = Db::name('goods_appraises')
							->field('goodsScore,serviceScore,timeScore,content,images,createTime')
							->where(['goodsId'=>$v['goodsId'],
							         'goodsSpecId'=>$v['goodsSpecId'],
									 'orderId'=>$orderId,
									 'dataFlag'=>1,
									 'isShow'=>1,
									 'userId'=>$userId
									 ])->find();
				if(!empty($appraise)){
					++$alreadys;
					$appraise['images'] = ($appraise['images']!='')?explode(',', $appraise['images']):[];
				}
				$goodsInfo[$k]['appraise'] = $appraise;
			}
		}
		return ['count'=>$count,'Rows'=>$goodsInfo,'alreadys'=>$alreadys];

	}
	
	/**
	 * 检查订单是否已支付
	 */
	public function checkOrderPay (){
		$userId = (int)session('WST_USER.userId');
		$orderId = input("id");
		$isBatch = (int)input("isBatch");
		$rs = array();
		$where = ["userId"=>$userId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1];
		if($isBatch==1){
			$where['orderunique'] = $orderId;
		}else{
			$where['orderId'] = $orderId;
		}
		$rs = $this->field('orderId,orderNo')->where($where)->select();
		if(count($rs)>0){
			return WSTReturn('',1);
		}else{
			return WSTReturn('订单已支付',-1);
		}
	}
}
