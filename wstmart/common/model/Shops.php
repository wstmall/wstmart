<?php
namespace wstmart\common\model;
use wstmart\home\model\ShopConfigs;
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
 * 门店类
 */
class Shops extends Base{
    /**
     * 获取商家认证
     */
    public function shopAccreds($shopId){
        $accreds= Db::table("__SHOP_ACCREDS__")->alias('sa')
        ->join('__ACCREDS__ a','a.accredId=sa.accredId','left')
        ->field('a.accredName,a.accredImg')
        ->where(['sa.shopId'=> $shopId])
        ->select();
        return $accreds;
    }

    /**
     * 获取店铺评分
     */
    public function getBriefShop($shopId){
        $shop = $this->alias('s')->join('__SHOP_SCORES__ cs','cs.shopId = s.shopId','left')
                    ->where(['s.shopId'=>$shopId,'s.shopStatus'=>1,'s.dataFlag'=>1])->field('s.shopImg,s.shopId,s.shopName,cs.*')->find();
        if(empty($shop))return [];
        $shop->toArray();
        $shop['totalScore'] = WSTScore($shop['totalScore']/3,$shop['totalUsers']);
        $shop['goodsScore'] = WSTScore($shop['goodsScore'],$shop['goodsUsers']);
        $shop['serviceScore'] = WSTScore($shop['serviceScore'],$shop['serviceUsers']);
        $shop['timeScore'] = WSTScore($shop['timeScore'],$shop['timeUsers']);
        WSTUnset($shop, 'totalUsers,goodsUsers,serviceUsers,timeUsers');
        return $shop;
    }
    /**
     * 获取店铺首页信息
     */
    public function getShopInfo($shopId){
    	$rs = $this->where(['shopId'=>$shopId,'shopStatus'=>1,'dataFlag'=>1])
    	->field('shopId,shopImg,shopName,shopAddress,shopQQ,shopWangWang,shopTel,serviceStartTime,serviceEndTime,shopKeeper')
    	->find();
    	if(empty($rs)){
    		//如果没有传id就获取自营店铺
    		$rs = $this->where(['shopStatus'=>1,'dataFlag'=>1,'isSelf'=>1])
    		->field('shopId,shopImg,shopName,shopAddress,shopQQ,shopWangWang,shopTel,serviceStartTime,serviceEndTime,shopKeeper')
    		->find();
    		if(empty($rs))return [];
    		$shopId = $rs['shopId'];
    	}
    	//评分
    	$score = $this->getBriefShop($rs['shopId']);
    	$rs['scores'] = $score;
    	//认证
    	$accreds = $this->shopAccreds($rs['shopId']);
    	$rs['accreds'] = $accreds;
    
    	$shopAds = array();
    	$config = Db::name('shop_configs')->where("shopId=".$rs['shopId'])->find();
    	$isAds = input('param.');
    	$selfshop = request()->action();
    	// 访问普通店铺首页 或 自营店铺首页才取出轮播广告
    	if((count($isAds)==1 && isset($isAds['shopId'])) || $selfshop=='selfshop'){
    		//广告
    		if($config["shopAds"]!=''){
    			$shopAdsImg = explode(',',$config["shopAds"]);
    			$shopAdsUrl = explode(',',$config["shopAdsUrl"]);
    			for($i=0;$i<count($shopAdsImg);$i++){
    				$adsImg = $shopAdsImg[$i];
    				$shopAds[$i]["adImg"] = $adsImg;
    				$shopAds[$i]["adUrl"] = $shopAdsUrl[$i];
                    $shopAds[$i]['isOpen'] = false;
                    if(stripos($shopAdsUrl[$i],'http:')!== false || stripos($shopAdsUrl[$i],'https:')!== false){
                     $shopAds[$i]['isOpen'] = true;
                }
    			}
    		}
    	}
    	$rs['shopAds'] = $shopAds;
    	$rs['shopTitle'] = $config["shopTitle"];
    	$rs['shopDesc'] = $config["shopDesc"];
    	$rs['shopKeywords'] = $config["shopKeywords"];
    	$rs['shopBanner'] = $config["shopBanner"];
    	//关注
    	$f = model('Favorites');
    	$rs['favShop'] = $f->checkFavorite($shopId,1);
    	//热搜关键词
    	$sc = new ShopConfigs();
    	$rs['shopHotWords'] = $sc->searchShopkey($shopId);
    	return $rs;
    }
    /**
     * 获取自营店铺 店长推荐 热卖商品
     */
    public function getRecGoods($type){
    	$arr = ['rec'=>'isRecom','hot'=>'isHot'];
    	$order='';
    	$where['g.dataFlag'] = 1;
    	$where['g.shopId'] = 1;
        $where['g.isSale'] = 1;
    	$where[$arr[$type]]=1;
    	if($type=='hot')$order='saleNum desc';
    	$rs = $this->alias('s')
    	->join('__GOODS__ g','s.shopId=g.shopId','inner')
    	->field('g.goodsName,g.goodsImg,g.shopPrice,g.goodsId')
    	->where($where)
    	->limit(5)
    	->order($order)
    	->select();
    	return $rs;
    }
}
