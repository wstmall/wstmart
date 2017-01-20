<?php
namespace wstmart\home\controller;
use wstmart\common\model\Orders as M;
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
 * 订单控制器
 */
class Orders extends Base{
    /**
    * 提交订单
    */
	public function submit(){
		$m = new M();
		$rs = $m->submit();
		return $rs;
	}
	/**
	 * 订单提交成功
	 */
	public function succeed(){
		$m = new M();
		$rs = $m->getByUnique();
		$this->assign('object',$rs);
		if(!empty($rs['list'])){
			if($rs['payType']==1){
				$this->assign('id',input("get.id"));
				$this->assign('isBatch',input("get.isBatch/d",1));
				$this->assign('rs',$rs);
				return $this->fetch('order_pay_step1');
			}else{
			    return $this->fetch('order_success');
			}
		}else{
			$this->assign('message','Sorry~您要找的页面丢失了。。。');
			return $this->fetch('error_msg');
		}
	}
	
	
	
	/**
	 * 用户-待付款订单
	 */
	public function waitPay(){
		return $this->fetch('users/orders/list_wait_pay');
	}
    /**
	 * 用户-获取待付款列表
	 */
    public function waitPayByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(-2);
		return WSTReturn("", 1,$rs);
	}
    /**
	 * 等待收货
	 */
	public function waitReceive(){
		return $this->fetch('users/orders/list_wait_receive');
	}
    /**
	 * 获取收货款列表
	 */
    public function waitReceiveByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage([0,1]);
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 用户-待评价
	 */
    public function waitAppraise(){
		return $this->fetch('users/orders/list_appraise');
	}
	/**
	 * 用户-待评价
	 */
	public function waitAppraiseByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(2,0);
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 用户-已完成订单
	 */
    public function finish(){
		return $this->fetch('users/orders/list_finish');
	}
	/**
	 * 用户-已完成订单
	 */
	public function finishByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(2,-1);
		return WSTReturn("", 1,$rs);
	}
   /**
	 * 用户-加载取消订单页面
	 */
	public function toCancel(){
		return $this->fetch('users/orders/box_cancel');
	}

	/**
	 * 用户取消订单
	 */
	public function cancellation(){
		$m = new M();
		$rs = $m->cancel();
		return $rs;
	}
    /**
	 * 用户-取消订单列表
	 */
	public function cancel(){
		return $this->fetch('users/orders/list_cancel');
	}
	/**
	 * 用户-获取已取消订单
	 */
    public function cancelByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(-1);
		return WSTReturn("", 1,$rs);
	}
    /**
	 * 用户-拒收订单
	 */
	public function toReject(){
		return $this->fetch('users/orders/box_reject');
	}
	/**
	 * 用户拒收订单
	 */
	public function reject(){
		$m = new M();
		$rs = $m->reject();
		return $rs;
	}
	/**
	 * 用户-申请退款
	 */
	public function toRefund(){
		$m = new M();
		$rs = $m->getMoneyByOrder((int)input('id'));
		$this->assign('object',$rs);
		return $this->fetch('users/orders/box_refund');
	}

	/**
	 * 商家-操作退款
	 */
	public function toShopRefund(){
		$rs = model('OrderRefunds')->getRefundMoneyByOrder((int)input('id'));
		$this->assign('object',$rs);
		return $this->fetch('shops/orders/box_refund');
	}
	
	/**
	 * 用户-拒收/退款列表
	 */
	public function abnormal(){
		return $this->fetch('users/orders/list_abnormal');
	}
	/**
	 * 获取用户拒收/退款列表
	 */
    public function abnormalByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage([-3,-4,-5]);
		return WSTReturn("", 1,$rs);
	}
	
	
	
    /**
	 * 等待处理订单
	 */
	public function waitDelivery(){
		$express = model('Express')->listQuery();
		$this->assign('express',$express);
		return $this->fetch('shops/orders/list_wait_delivery');
	}
	/**
	 * 待处理订单
	 */
	public function waitDeliveryByPage(){
		$m = new M();
		$rs = $m->shopOrdersByPage([0]);
		return WSTReturn("", 1,$rs);
	}

	/**
	* 商家-已发货订单
	*/
	public function delivered(){
		$express = model('Express')->listQuery();
		$this->assign('express',$express);
		return $this->fetch('shops/orders/list_delivered');
	}
	/**
	 * 待处理订单
	 */
	public function deliveredByPage(){
		$m = new M();
		$rs = $m->shopOrdersByPage([1,2]);
		return WSTReturn("", 1,$rs);
	}

    /**
	 * 商家发货
	 */
	public function deliver(){
		$m = new M();
		$rs = $m->deliver();
		return $rs;
	}
	/**
	 * 用户收货
	 */
	public function receive(){
		$m = new M();
		$rs = $m->receive();
		return $rs;
	}
	/**
	 * 商家-已完成订单
	 */
    public function finished(){
		$express = model('Express')->listQuery();
		return $this->fetch('shops/orders/list_finished');
	}
	/**
	 * 商家-已完成订单
	 */
	public function finishedByPage(){
		$m = new M();
		$rs = $m->shopOrdersByPage(2);
		return WSTReturn("", 1,$rs);
	}
    /**
	 * 商家-取消/拒收订单
	 */
    public function failure(){
		return $this->fetch('shops/orders/list_failure');
	}
	/**
	 * 商家-取消/拒收订单
	 */
	public function failureByPage(){
		$m = new M();
		$rs = $m->shopOrdersByPage([-1,-3,-4,-5]);
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 获取订单信息方便修改价格
	 */
	public function getMoneyByOrder(){
		$m = new M();
		$rs = $m->getMoneyByOrder();
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 商家修改订单价格
	 */
	public function editOrderMoney(){
		$m = new M();
		$rs = $m->editOrderMoney();
		return $rs;
	}
	/**
	 * 商家-订单详情
	 */
	public function view(){
		$m = new M();
		$rs = $m->getByView((int)input('id'));
		$this->assign('object',$rs);
		return $this->fetch('shops/orders/view');
	}
	/**
	 * 订单打印
	 */
	public function orderPrint(){
        $m = new M();
		$rs = $m->getByView((int)input('id'));
		$this->assign('object',$rs);
		return $this->fetch('shops/orders/print');
	}

    /**
	 * 用户-订单详情
	 */
	public function detail(){
		$m = new M();
		$rs = $m->getByView((int)input('id'));
		$this->assign('object',$rs);
		return $this->fetch('users/orders/view');
	}
	
   /**
	* 用户-评价页
	*/
	public function orderAppraise(){
		$m = new M();
		//根据订单id获取 商品信息跟商品评价
		$data = $m->getOrderInfoAndAppr();
		$this->assign(['data'=>$data['Rows'],
					   'count'=>$data['count'],
					   'alreadys'=>$data['alreadys']
						]);
		return $this->fetch('users/orders/list_order_appraise');
	}
	/**
	* 设置完成评价
	*/
	public function complateAppraise($orderId){
		$m = new M();
		return $m->complateAppraise($orderId);
	}
	/**
	 * 商家-待付款订单
	 */
	public function waituserPay(){
		return $this->fetch('shops/orders/list_wait_pay');
	}
	/**
	 * 商家-获取待付款列表
	 */
	public function waituserPayByPage(){
		$m = new M();
		$rs = $m->shopOrdersByPage(-2);
		return WSTReturn("", 1,$rs);
	}
}
