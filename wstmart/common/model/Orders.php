<?php
namespace wstmart\common\model;
use think\Db;
use think\Loader;
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
		if($userId==0)return WSTReturn('下单失败,请先登录系统');
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
		$orderNo = input('orderNo');
		$isBatch = (int)input('isBatch/d',1);
		$userId = (int)session('WST_USER.userId');
		if($isBatch==1){
			$rs = $this->where(['userId'=>$userId,'orderunique'=>$orderNo])->field('orderId,orderNo,payType,needPay,orderunique,deliverMoney')->select();
		}else{
			$rs = $this->where(['userId'=>$userId,'orderNo'=>$orderNo])->field('orderId,orderNo,payType,needPay,orderunique,deliverMoney')->select();
		}
		
		$data = [];
		$data['orderunique'] = $orderNo;
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
						// 增加用户积分
						model('Users')->where("userId=$userId")->setInc('userScore',$order['orderScore']);
						// 用户总积分
						model('Users')->where("userId=$userId")->setInc('userTotalScore',$order['orderScore']);
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
		$orderNo = input("orderNo");
		$isBatch = (int)input("isBatch");
		$rs = array();
		$where = ["userId"=>$userId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1];
		if($isBatch==1){
			$where['orderunique'] = $orderNo;
		}else{
			$where['orderNo'] = $orderNo;
		}
		$rs = $this->field('orderId,orderNo')->where($where)->select();
		if(count($rs)>0){
			return WSTReturn('',1);
		}else{
			return WSTReturn('订单已支付',-1);
		}
	}

	/**
	 * 导出订单
	 */
	public function toExport(){
		$name='订单表';
		$where = ['o.dataFlag'=>1];
		$orderStatus = (int)input('orderStatus',0);
		if($orderStatus==0){
			$name='待发货订单表';
		}else if($orderStatus==-2){
			$name='待付款订单表';
		}else if($orderStatus==1){
			$name='配送中订单表';
		}else if($orderStatus==-1){
			$name='取消订单表';
		}else if($orderStatus==-3){
			$name='拒收订单表';
		}else if($orderStatus==2){
			$name='已收货订单表';
		}else if($orderStatus==10000){
			$name='取消/拒收订单表';
		}else if($orderStatus==20000){
			$name='待收货订单表';
		}
		$typeId = (int)input('typeId',0);
		if($typeId==1){
			$userId = (int)session('WST_USER.userId');
			$where = ['o.userId'=>$userId];
		}else{
			$shopId = (int)session('WST_USER.shopId');
			$where = ['o.shopId'=>$shopId];
		}
		$orderNo = input('orderNo');
		$shopName = input('shopName');
		
		$type = (int)input('type',-1);
		$payType = $type>0?$type:(int)input('payType',-1);
		$deliverType = (int)input('deliverType');
		if($orderStatus == 10000)$orderStatus = [-1,-3];
		if($orderStatus == 20000)$orderStatus = [0,1];
		if(is_array($orderStatus)){
			$where['o.orderStatus'] = ['in',$orderStatus];
		}else{
			$where['o.orderStatus'] = $orderStatus;
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
		$page = $this->alias('o')->where($where)->join('__SHOPS__ s','o.shopId=s.shopId','left')
		->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and refundStatus=0','left')
		->join('__LOG_ORDERS__ lo','lo.orderId=o.orderId and lo.orderStatus in (-1,-3) ','left')
		->field('o.orderId,orderNo,goodsMoney,totalMoney,realTotalMoney,o.orderStatus,deliverType,deliverMoney,isAppraise,o.deliverMoney,lo.logContent
		              ,payType,o.userName,o.userAddress,o.userPhone,o.orderRemarks,o.invoiceClient,o.receiveTime,o.deliveryTime,orderSrc,o.createTime,orf.id refundId')
				              ->order('o.createTime', 'desc')
				               ->select();
		if(count($page)>0){
			foreach ($page as $key => $v){
				$page[$key]['payTypeName'] = WSTLangPayType($v['payType']);
				$page[$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
				$page[$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
			}
		}
		Loader::import('phpexcel.PHPExcel.IOFactory');
		$objPHPExcel = new \PHPExcel();
		// 设置excel文档的属性
		$objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
		->setLastModifiedBy("WSTMart")//最后修改人
		->setTitle($name)//标题
		->setSubject($name)//题目
		->setDescription($name)//描述
		->setKeywords("订单")//关键字
		->setCategory("Test result file");//种类
	
		// 开始操作excel表
		$objPHPExcel->setActiveSheetIndex(0);
		// 设置工作薄名称
		$objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
		// 设置默认字体和大小
		$objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
		$styleArray = array(
				'font' => array(
						'bold' => true,
						'color'=>array(
								'argb' => 'ffffffff',
						)
				),
				'borders' => array (
						'outline' => array (
								'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
								'color' => array ('argb' => 'FF000000'),     //设置border颜色
						)
				)
		);
		//设置宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFill()->getStartColor()->setARGB('333399');
	
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '订单编号')->setCellValue('B1', '订单状态')->setCellValue('C1', '收货人')->setCellValue('D1', '收货地址')->setCellValue('E1', '联系方式')
		->setCellValue('F1', '支付方式')->setCellValue('G1', '配送方式')->setCellValue('H1', '买家留言')->setCellValue('I1', '发票信息')->setCellValue('J1', '订单总金额')->setCellValue('K1', '运费')
		->setCellValue('L1', '实付金额')->setCellValue('M1', '下单时间')->setCellValue('N1', '发货时间')->setCellValue('O1', '收货时间')->setCellValue('P1', '取消/拒收原因');
		$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
	
		for ($row = 0; $row < count($page); $row++){
			$i = $row+2;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $page[$row]['orderNo'])->setCellValue('B'.$i, $page[$row]['status'])->setCellValue('C'.$i, $page[$row]['userName'])->setCellValue('D'.$i, $page[$row]['userAddress'])
			->setCellValue('E'.$i, $page[$row]['userPhone'])->setCellValue('F'.$i, $page[$row]['payTypeName'])->setCellValue('G'.$i, $page[$row]['deliverType'])->setCellValue('H'.$i, $page[$row]['orderRemarks'])->setCellValue('I'.$i, $page[$row]['invoiceClient'])
			->setCellValue('J'.$i, $page[$row]['totalMoney'])->setCellValue('K'.$i, $page[$row]['deliverMoney'])->setCellValue('L'.$i, $page[$row]['realTotalMoney'])->setCellValue('M'.$i, $page[$row]['createTime'])->setCellValue('N'.$i, $page[$row]['deliveryTime'])
			->setCellValue('O'.$i, $page[$row]['receiveTime'])->setCellValue('P'.$i, $page[$row]['logContent']);
		}
	
		//输出EXCEL格式
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		// 从浏览器直接输出$filename
		header('Content-Type:application/csv;charset=UTF-8');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-excel;");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header('Content-Disposition: attachment;filename="'.$name.'.xls"');
		header("Content-Transfer-Encoding:binary");
		$objWriter->save('php://output');
	}
}
