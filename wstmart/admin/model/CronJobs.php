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
 * 定时业务处理
 */
class CronJobs extends Base{
	/**
	 * 管理员登录触发动作
	 */
	public function autoByAdmin(){
		$this->autoCancelNoPay();
		$this->autoReceive();
		$this->autoAppraise();
	}
	/**
	 * 取消未支付订单
	 */
	public function autoCancelNoPay(){
		$autoCancelNoPayDays = (int)WSTConf('CONF.autoCancelNoPayDays');
	 	$autoCancelNoPayDays = ($autoCancelNoPayDays>0)?$autoCancelNoPayDays:6;
	 	$lastDay = date("Y-m-d H:i:s",strtotime("-".$autoCancelNoPayDays." hours"));
	 	$orders = Db::name('orders')->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')->where("o.createTime<'".$lastDay."' and o.orderStatus=-2 and o.dataFlag=1 and o.payType=1 and o.isPay=0")->field("o.orderId,o.orderNo,o.userId,o.shopId,s.userId shopUserId")->select();
	 	if(!empty($orders)){
	 		$prefix = config('database.prefix');
	 		$orderIds = [];
	 		foreach ($orders as $okey => $order){
	 			$orderIds[] = $order['orderId'];
	 		}
	 		Db::startTrans();
		    try{
		    	//提前锁定订单
		    	Db::name('orders')->where(['orderId'=>['in',$orderIds]])->update(['orderStatus'=>-1]);
                foreach ($orders as $okey => $order){
                	$goods = Db::name('order_goods')->alias('og')->join('__GOODS__ g','og.goodsId=g.goodsId','inner')
					           ->where('orderId',$order['orderId'])->field('og.*,g.isSpec')->select();
					foreach ($goods as $k => $v){
						//修改库存
						if($v['isSpec']>0){
					        Db::name('goods_specs')->where('id',$v['goodsSpecId'])->setInc('specStock',$v['goodsNum']);
						}
						Db::name('goods')->where('goodsId',$v['goodsId'])->setInc('goodsStock',$v['goodsNum']);
					}
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $order['orderId'];
					$logOrder['orderStatus'] = -1;
					$logOrder['logContent'] = "订单长时间未支付，系统自动取消订单";
					$logOrder['logUserId'] = $order['userId'];
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//发送一条商家信息
					WSTSendMsg($order['shopUserId'],"订单【".$order['orderNo']."】因长时间未支付，系统自动取消订单",['from'=>1,'dataId'=>$order['orderId']]);
					//发送一条用户信息
					WSTSendMsg($order['userId'],"订单【".$order['orderNo']."】因长时间未支付，系统自动取消订单",['from'=>1,'dataId'=>$order['orderId']]);
                }
		        Db::commit();
				return WSTReturn('操作成功',1);
	 		}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
	        return WSTReturn('操作成功',1);
	 	}
	}
    /**
	 * 自动好评
	 */
	public function autoAppraise(){
        $autoAppraiseDays = (int)WSTConf('CONF.autoAppraiseDays');
	 	$autoAppraiseDays = ($autoAppraiseDays>0)?$autoAppraiseDays:7;//避免有些客户没有设置值
	 	$lastDay = date("Y-m-d 00:00:00",strtotime("-".$autoAppraiseDays." days"));
	 	$rs = model('orders')->where("receiveTime<'".$lastDay."' and orderStatus=2 and dataFlag=1 and isAppraise=0")->field("orderId,userId,orderScore,shopId,orderNo")->select();
	 	if(!empty($rs)){
	 		$prefix = config('database.prefix');
	 		$orderIds = [];
	 		foreach ($rs as $okey => $order){
	 			$orderIds[] = $order->orderId;
	 		}
	 		Db::startTrans();
		    try{
		    	//提前锁定订单
		    	Db::name('orders')->where(['orderId'=>['in',$orderIds]])->update(['isAppraise'=>1,'isClosed'=>1]);
		    	foreach ($rs as $okey => $order){;
		    	    //获取订单相关的商品
		    	    $ordergoods = Db::name('order_goods')->where('orderId',$order->orderId)->field('goodsId,orderId,goodsSpecId')->select();
		    	    foreach($ordergoods as $goods){
		    	    	//增加订单评价
						$data = [];
						$data['userId'] = $order->userId;
						$data['goodsSpecId'] = $goods['goodsSpecId'];
						$data['goodsId'] = $goods['goodsId'];
						$data['shopId'] = $order->shopId;
						$data['orderId'] = $goods['orderId'];
						$data['goodsScore'] = 5;
						$data['serviceScore'] = 5;
						$data['timeScore']= 5;
						$data['content'] = '自动好评';
						$data['createTime'] = date('Y-m-d H:i:s');
						Db::name('goods_appraises')->insert($data);
		    	    }
					//增加商品评分
					$updateSql = "update ".$prefix."goods_scores set 
						             totalScore=totalScore+15,
					             goodsScore=goodsScore+5,
					             serviceScore=serviceScore+5,
					             timeScore=timeScore+5,
					             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
					             where goodsId=".$goods['goodsId'];
					Db::execute($updateSql);
					//增加商品评价数
					Db::name('goods')->where('goodsId',$goods['goodsId'])->setInc('appraiseNum');
					//增加店铺评分
					$updateSql = "update ".$prefix."shop_scores set 
					             totalScore=totalScore+15,
					             goodsScore=goodsScore+5,
					             serviceScore=serviceScore+5,
					             timeScore=timeScore+5,
					             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
					             where shopId=".$order->shopId;
					Db::execute($updateSql);
					// 查询该订单是否已经完成评价,修改orders表中的isAppraise
					$ogRs = Db::name('order_goods')->alias('og')
					   			  ->join('__GOODS_APPRAISES__ ga','og.orderId=ga.orderId and og.goodsId=ga.goodsId and og.goodsSpecId=ga.goodsSpecId','left')
					              ->where('og.orderId',$order->orderId)->field('og.id,ga.id gid')->select();
					$isFinish = true;
					foreach ($ogRs as $vkey => $v){
						if($v['id']>0 && $v['gid']==''){
								$isFinish = false;
								break;
						}
					}
					//订单商品全部评价完则修改订单状态
					if($isFinish){
						if(WSTConf("CONF.isAppraisesScore")==1){
							//给用户增加积分
							$score = [];
							$score['userId'] = $order->userId;
							$score['score'] = 5;
							$score['dataSrc'] = 1;
							$score['dataId'] = $order->orderId;
							$score['dataRemarks'] = "评价订单【".$order->orderNo."】获得积分5个";
							$score['scoreType'] = 1;
							$score['createTime'] = date('Y-m-d H:i:s');
							Db::name('user_scores')->insert($score);
							// 增加用户积分
						    model('Users')->where("userId=".$order->userId)->setInc('userScore',5);
						    // 用户总积分
						    model('Users')->where("userId=".$order->userId)->setInc('userTotalScore',5);
						}
					}
				}
		        Db::commit();
				return WSTReturn('操作成功',1);
	 		}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
	        return WSTReturn('操作成功',1);
	 	}
	}
	/**
	 * 自动确认收货
	 */
	public function autoReceive(){
	 	$autoReceiveDays = (int)WSTConf('CONF.autoReceiveDays');
	 	$autoReceiveDays = ($autoReceiveDays>0)?$autoReceiveDays:10;//避免有些客户没有设置值
	 	$lastDay = date("Y-m-d 00:00:00",strtotime("-".$autoReceiveDays." days"));
	 	$rs = model('orders')->where("deliveryTime<'".$lastDay."' and orderStatus=1 and dataFlag=1")->field("orderId,orderNo,shopId,userId,shopId,orderScore")->select();
	 	if(!empty($rs)){
	 		$prefix = config('database.prefix');
	 		Db::startTrans();
		    try{
		 		foreach ($rs as $key => $order){
		 			//计算订单佣金
					$goods = Db::name('order_goods')->field('goodsNum,goodsPrice,commissionRate')->where('orderId',$order->orderId)->select();
					$commissionFee = 0;
					foreach ($goods as $gkey => $v) {
						if((float)$v['commissionRate']>0){
							$commissionFee += round($v['goodsPrice']*$v['goodsNum']*$v['commissionRate']/100,2);
						}
					}
		 			//结束订单状态
		 			$order->receiveTime = date('Y-m-d 00:00:00');
		 			$order->orderStatus = 2;
		 			$order->commissionFee = $commissionFee;
		 			$rsStatus = $order->save();
		 			if(false !== $rsStatus){
		 				//修改商家未计算订单数
						$upSql = 'update '.$prefix.'shops set noSettledOrderNum=noSettledOrderNum+1,noSettledOrderFee=noSettledOrderFee-'.$commissionFee.' where shopId='.$order->shopId;
						Db::execute($upSql);
	                    //新增订单日志
						$logOrder = [];
						$logOrder['orderId'] = $order->orderId;
						$logOrder['orderStatus'] = 2;
						$logOrder['logContent'] = "系统自动确认收货";
						$logOrder['logUserId'] = $order->userId;
						$logOrder['logType'] = 0;
						$logOrder['logTime'] = date('Y-m-d H:i:s');
						Db::name('log_orders')->insert($logOrder);
						//发送一条商家信息
						WSTSendMsg($order['userId'],"您的订单【".$order['orderNo']."】已自动确认收货",['from'=>1,'dataId'=>$order->orderId]);
						//给用户增加积分
						if(WSTConf("CONF.isOrderScore")==1){
							$score = [];
							$score['userId'] = $order->userId;
							$score['score'] = $order->orderScore;
							$score['dataSrc'] = 1;
							$score['dataId'] = $order->orderId;
							$score['dataRemarks'] = "交易订单【".$order->orderNo."】获得积分".$order->orderScore."个";
							$score['scoreType'] = 1;
							$score['createTime'] = date('Y-m-d H:i:s');
							model('UserScores')->save($score);
							// 增加用户积分
						    model('Users')->where("userId=".$order->userId)->setInc('userScore',$order->orderScore);
						    // 用户总积分
						    model('Users')->where("userId=".$order->userId)->setInc('userTotalScore',$order->orderScore);
						}
		 			}
	 			}
	 			Db::commit();
				return WSTReturn('操作成功',1);
	 		}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
	 	}
	 	return WSTReturn('操作成功',1);
	}
}