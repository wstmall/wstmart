<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Recommends as M;
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
 * 推荐管理控制器
 */
class Recommends extends Base{
    /**
    * 查看商品推荐
    */
	public function goods(){
		return $this->fetch('goods');
	}
	/**
	 * 查询商品
	 */
	public function searchGoods(){
		$rs = model('Goods')->searchQuery();
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 推荐商品
	 */
	public function editGoods(){
		$m = new M();
		return $m->editGoods();
	}
	/**
	 * 获取已选择商品
	 */
	public function listQueryByGoods(){
		$m = new M();
		$rs= $m->listQueryByGoods();
		return WSTReturn("", 1,$rs);
	}
	
    /**
    * 查看店铺推荐
    */
	public function shops(){
		return $this->fetch('shops');
	}
	/**
	 * 查询店铺
	 */
	public function searchShops(){
		$rs = model('Shops')->searchQuery();
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 推荐店铺
	 */
	public function editShops(){
		$m = new M();
		return $m->editShops();
	}
	/**
	 * 获取已选择店铺
	 */
	public function listQueryByShops(){
		$m = new M();
		$rs= $m->listQueryByShops();
		return WSTReturn("", 1,$rs);
	}
	
	
   /**
    * 查看品牌推荐
    */
	public function brands(){
		return $this->fetch('brands');
	}
	/**
	 * 查询品牌
	 */
	public function searchBrands(){
		$rs = model('Brands')->searchBrands();
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 推荐品牌
	 */
	public function editBrands(){
		$m = new M();
		$rs= $m->editBrands();
		return $rs;
	}
	/**
	 * 获取已选择品牌
	 */
	public function listQueryByBrands(){
		$m = new M();
		$rs= $m->listQueryByBrands();
		return WSTReturn("", 1,$rs);
	}
}
