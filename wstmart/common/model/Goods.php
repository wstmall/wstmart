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
 * 商品类
 */
class Goods extends Base{
	/**
	 * 获取店铺商品列表
	 */
	public function shopGoods($shopId){
		$msort = input("param.msort/d");
		$mdesc = input("param.mdesc/d");
		$order = array('g.saleTime'=>'desc');
		$orderFile = array('1'=>'g.isHot','2'=>'g.saleNum','3'=>'g.shopPrice','4'=>'g.shopPrice','5'=>'(gs.totalScore/gs.totalUsers)','6'=>'g.saleTime');
		$orderSort = array('0'=>'asc','1'=>'desc');
		if($msort>0){
			$order = array($orderFile[$msort]=>$orderSort[$mdesc]);
		}
		$goodsName = input("param.goodsName");//搜索店鋪名
		$words = $where = $where2 = $where3 = [];
		if($goodsName!=""){
			$words = explode(" ",$goodsName);
		}
		if(!empty($words)){
			$sarr = array();
			foreach ($words as $key => $word) {
				if($word!=""){
					$sarr[] = "g.goodsName like '%$word%'";
				}
			}
			$where = implode(" or ", $sarr);
		}
		$sprice = input("param.sprice");//开始价格
		$eprice = input("param.eprice");//结束价格
		if($sprice!="")$where2 = "g.shopPrice >= ".(float)$sprice;
		if($eprice!="")$where3 = "g.shopPrice <= ".(float)$eprice;
		$ct1 = input("param.ct1/d");
		$ct2 = input("param.ct2/d");
		if($ct1>0)$where['shopCatId1'] = $ct1;
		if($ct2>0)$where['shopCatId2'] = $ct2;
		$goods = Db::name('goods')->alias('g')
		->join('__GOODS_SCORES__ gs','gs.goodsId = g.goodsId','left')
		->where(['g.shopId'=>$shopId,'g.isSale'=>1,'g.goodsStatus'=>1,'g.dataFlag'=>1])
		->where($where)->where($where2)->where($where3)
		->field('g.goodsId,g.goodsName,g.goodsImg,g.shopPrice,g.marketPrice,g.saleNum,g.appraiseNum,g.goodsStock')
		->order($order)
		->paginate(20)->toArray();
		return  $goods;
	}
}
