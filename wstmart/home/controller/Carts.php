<?php
namespace wstmart\home\controller;
use wstmart\common\model\Carts as M;
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
 * 购物车控制器
 */
class Carts extends Base{
    /**
    * 查看商城消息
    */
	public function addCart(){
		$m = new M();
		$rs = $m->addCart();
		return $rs;
	}
	/**
	 * 查看购物车列表
	 */
	public function index(){
		$m = new M();
		$carts = $m->getCarts(false);
		$this->assign('carts',$carts);
		return $this->fetch('carts');
	}
	/**
	 * 删除购物车里的商品
	 */
	public function delCart(){
		$m = new M();
		$rs= $m->delCart();
		return $rs;
	}
	/**
	 * 跳去购物车结算页面
	 */
    public function settlement(){
		$m = new M();
		//获取一个用户地址
		$userAddress = model('UserAddress')->getDefaultAddress();
		$this->assign('userAddress',$userAddress);
		//获取省份
		$areas = model('Areas')->listQuery();
		$this->assign('areaList',$areas);
		//获取支付方式
		$payments = model('Payments')->getByGroup();
		$this->assign('payments',$payments);
		//获取已选的购物车商品
		$carts = $m->getCarts(true);
		$this->assign('carts',$carts);
		return $this->fetch('settlement');
	}
	
	/**
	 * 计算运费和总商品价格
	 */
	public function getCartMoney(){
		$areaId = input('post.areaId2/d',-1);
		//获取已选的购物车商品
		$m = new M();
		$carts = $m->getCarts(true);
		$shopFreight = 0;
		$data = ['shops'=>[],'total'=>0,'status'=>1];
		foreach ($carts['carts'] as $key =>$v){
			$shopFreight = WSTOrderFreight($v['shopId'],$areaId);
			$data['shops'][$v['shopId']] = $shopFreight;
			$data['total'] += $v['goodsMoney'] + $shopFreight;
		}
		return $data;
	}
	/**
	 * 修改购物车商品状态
	 */
	public function changeCartGoods(){
		$m = new M();
		$rs = $m->changeCartGoods();
		return $rs;
	}
	/**
	 * 获取购物车商品
	 */
    public function getCart(){
		$m = new M();
		$carts = $m->getCarts(false);
		return WSTReturn("", 1,$carts);;
	}
	/**
	 * 获取购物车信息
	 */
	public function getCartInfo(){
		$m = new M();
		$rs = $m->getCartInfo();
		return WSTReturn("", 1,$rs);
	}
}
