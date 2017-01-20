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
 * 标签业务处理类
 */
class Tags extends Base{
	/**
	 * 获取指定商品
	 */
	public function listGoods($type,$catId = 0,$num,$cache = 0){
		$type = strtolower($type);
		if(strtolower($type)=='history'){
			return $this->historyByGoods($num);
		}else{
			return $this->listByGoods($type,$catId,$num,$cache);
		}
	}
	/**
	 * 浏览商品
	 */
	public function historyByGoods($num){
		$hids = $ids = cookie("history_goods");
		if(empty($ids))return [];
	    $where = [];
	    $where['isSale'] = 1;
	    $where['goodsStatus'] = 1; 
	    $where['g.dataFlag'] = 1; 
	    $where['goodsId'] = ['in',$ids];
        $goods = Db::name('goods')->alias('g')->join('__SHOPS__ s','g.shopId=s.shopId')
                   ->where($where)->field('s.shopName,s.shopId,goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum')
                   ->limit($num)
                   ->select(); 
        $ids = [];
        foreach($goods as $key =>$v){
        	if($v['isSpec']==1)$ids[] = $v['goodsId'];
        }
        if(!empty($ids)){
        	$specs = [];
        	$rs = Db::name('goods_specs gs ')->where(['goodsId'=>['in',$ids],'dataFlag'=>1])->order('id asc')->select();
        	foreach ($rs as $key => $v){
        		$specs[$v['goodsId']] = $v;
        	}
        	foreach($goods as $key =>$v){
        		if(isset($specs[$v['goodsId']]))
        		$goods[$key]['specs'] = $specs[$v['goodsId']];
        	}
        }
        $hGoods = [];
        foreach($hids as $k=>$v){
        	foreach($goods as $k1=>$v1){
        		if($v1['goodsId']==$v)$hGoods[] = $v1;
        	}
        }
        return $hGoods;
	}
	/**
	 * 推荐商品
	 */
	public function listByGoods($type,$catId,$num,$cache = 0){
		if(!in_array($type,[0,1,2,3]))return [];
		$cacheData = cache('TAG_GOODS_'.$type."_".$catId."_".$num);
		if($cacheData)return $cacheData;
		//检测是否有数据
		$types = ['recom'=>0,'new'=>3,'hot'=>1,'best'=>2];
        $where = [];
        $where['r.dataSrc'] = 0;
        $where['g.isSale'] = 1;
        $where['g.goodsStatus'] = 1; 
        $where['g.dataFlag'] = 1; 
        $goods=[];
        if($type!='visit'){
	        $where['r.dataType'] = $types[$type];
	        $where['r.goodsCatId'] = $catId;
	        $goods = Db::name('goods')->alias('g')->join('__RECOMMENDS__ r','g.goodsId=r.dataId')
	                   ->join('__SHOPS__ s','g.shopId=s.shopId')
	                   ->where($where)->field('s.shopName,s.shopId,g.goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum')
	                   ->order('r.dataSort asc')->limit($num)->select();
        }
        //判断有没有设置，如果没有设置的话则获取实际的数据
	    if(empty($goods)){
	    	$goodsCatIds = WSTGoodsCatPath($catId);
	    	$types = ['recom'=>'isRecom','new'=>'isNew','hot'=>'isHot','best'=>'isBest'];
	    	$order = ['recom'=>'saleNum desc,goodsId asc',
	    			  'new'=>'saleTime desc,goodsId asc',
	    			  'hot'=>'saleNum desc,goodsId asc',
	    			  'best'=>'saleNum desc,goodsId asc',
	    			  'visit'=>'visitNum desc'
	    			 ];

	    	$where = [];
	        $where['isSale'] = 1;
	        $where['goodsStatus'] = 1; 
	        $where['g.dataFlag'] = 1; 

	        if($type!='visit')
	        $where[$types[$type]] = 1;



	        if(!empty($goodsCatIds))$where['g.goodsCatIdPath'] = ['like',implode('_',$goodsCatIds).'_%'];
        	$goods = Db::name('goods')->alias('g')->join('__SHOPS__ s','g.shopId=s.shopId')
                   ->where($where)->field('s.shopName,s.shopId,goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum')
                   ->order($order[$type])->limit($num)->select();
        }   
        $ids = [];
        foreach($goods as $key =>$v){
        	if($v['isSpec']==1)$ids[] = $v['goodsId'];
        }
        if(!empty($ids)){
        	$specs = [];
        	$rs = Db::name('goods_specs gs ')->where(['goodsId'=>['in',$ids],'dataFlag'=>1])->order('id asc')->select();
        	foreach ($rs as $key => $v){
        		$specs[$v['goodsId']] = $v;
        	}
        	foreach($goods as $key =>$v){
        		if(isset($specs[$v['goodsId']]))
        		$goods[$key]['specs'] = $specs[$v['goodsId']];
        	}
        }
        cache('TAG_GOODS_'.$type."_".$catId."_".$num,$goods,$cache);
        return $goods;
	}
	
	/**
	 * 获取广告位置
	 */
	public function listAds($positionCode,$num,$cache = 0){
		$cacheData = cache('TAG_ADS'.$positionCode);
		if($cacheData)return $cacheData;
		$today = date('Y-m-d');
		$rs = Db::name("ads")->alias('a')->join('__AD_POSITIONS__ ap','a.adPositionId= ap.positionId and ap.dataFlag=1','left')
		          ->where("a.dataFlag=1 and ap.positionCode='".$positionCode."' and adStartDate<= '$today' and adEndDate>='$today'")
		          ->field('adId,adName,adURL,adFile,positionWidth,positionHeight')
		          ->order('adSort asc')->limit($num)->select();
		if(count($rs)>0){
			foreach ($rs as $key => $v) {
				 $rs[$key]['isOpen'] = false;
				if(stripos($v['adURL'],'http:')!== false || stripos($v['adURL'],'https:')!== false){
                     $rs[$key]['isOpen'] = true;
				}
			}
		}
		cache('TAG_ADS'.$positionCode,$rs,$cache);
		return $rs;
	}
	
	/**
	 * 获取友情链接
	 */
	public function listFriendlink($num,$cache = 0){
		$cacheData = cache('TAG_FRIENDLINK');
		if($cacheData)return $cacheData;
		$rs = Db::name("friendlinks")->where(["dataFlag"=>1])->order("friendlinkSort asc")->select();
		cache('TAG_FRIENDLINK',$rs,$cache);
	    return $rs;
	}
	
    /**
	 * 获取文章列表
	 */
	public function listArticle($catId,$num,$cache = 0){
		$cacheData = cache('TAG_ARTICLES_'.$catId."_".$num);
		if($cacheData)return $cacheData;
		$rs = [];
		if($catId=='new'){
			$rs = $this->listByNewArticle($num,$cache);
		}else{
			$rs = $this->listByArticle($catId,$num,$cache);
		}
		cache('TAG_ARTICLES_'.$catId."_".$num,$rs,$cache);
		return $rs;
	}
    /**
	 * 获取最新文章
	 */
	public function listByNewArticle($num,$cache){
		$cacheData = cache('TAG_NEW_ARTICLES');
		if($cacheData)return $cacheData;
		$rs = Db::name('articles')->alias('a')->field('a.articleId,a.articleTitle')->join('article_cats ac','a.catId=ac.catId','inner')
		            ->where('a.catId<>7 and ac.parentId<>7 and a.dataFlag=1')->order('a.createTime','desc')->limit($num)->select();
		cache('TAG_NEW_ARTICLES',$rs,$cache);
	    return $rs;
	}
	/**
	 * 获取指定分类的文章
	 */
	public function listByArticle($catId,$num,$cache){
		$where = [];
		$where['dataFlag'] = 1;
		$where['isShow'] = 1;
		if(is_array($catId)){
		    $where['catId'] = ['in',$catId];
		}else{
			$where['catId'] = $catId;
		}
		return Db::name('articles')->where($where)
		         ->field("articleId, catId, articleTitle")->order('createTime desc')->limit($num)->select(); 
	}
	
    /**
	 * 获取指定店铺商品
	 */
	public function listShopGoods($type,$shopId,$num,$cache = 0){
		$cacheData = cache('TAG_SHOP_GOODS_'.$type."_".$shopId);
		if($cacheData)return $cacheData;
		if(!in_array($type,[0,1,2,3]))return [];
	    $types = ['recom'=>'isRecom','new'=>'isNew','hot'=>'isHot','best'=>'isBest'];
	    $order = ['recom'=>'saleNum desc,goodsId asc','new'=>'saleTime desc,goodsId asc','hot'=>'saleNum desc,goodsId asc','best'=>'saleNum desc,goodsId asc'];
	    $where = [];
	    $where['shopId'] = $shopId;
	    $where['isSale'] = 1;
	    $where['goodsStatus'] = 1; 
	    $where['dataFlag'] = 1; 
	    $where[$types[$type]] = 1;
        $goods = Db::name('goods')
                   ->where($where)->field('goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum')
                   ->order($order[$type])->limit($num)->select();       
        $ids = [];
        foreach($goods as $key =>$v){
        	if($v['isSpec']==1)$ids[] = $v['goodsId'];
        }
        if(!empty($ids)){
        	$specs = [];
        	$rs = Db::name('goods_specs gs ')->where(['goodsId'=>['in',$ids],'dataFlag'=>1])->order('id asc')->select();
        	foreach ($rs as $key => $v){
        		$specs[$v['goodsId']] = $v;
        	}
        	foreach($goods as $key =>$v){
        		if(isset($specs[$v['goodsId']]))
        		$goods[$key]['specs'] = $specs[$v['goodsId']];
        	}
        }
        cache('TAG_SHOP_GOODS_'.$type."_".$shopId,$goods,$cache);
        return $goods;
	}
	/**
	* 获取店铺分类下的商品
	*/
	public function listShopFloorGoods($catId,$shopId,$num,$cache = 0){
		$cacheData = cache('TAG_SHOP_CAT_GOODS_'.$catId."_".$shopId);
		if($cacheData)return $cacheData;
	    $where = [];
	    $where['shopId'] = $shopId;
	    $where['isSale'] = 1;
	    $where['goodsStatus'] = 1; 
	    $where['dataFlag'] = 1;
	    $where['shopCatId2'] = $catId;
		$goods = Db::name('goods')
                   ->where($where)->field('goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum')
                   ->limit($num)->select();
        cache('TAG_SHOP_CAT_GOODS_'.$catId."_".$shopId,$goods,$cache);
        return $goods;
	}

	/**
	* 获取分类下的品牌
	*/
	public function listBrand($catId,$num,$cache = 0){
		$cacheData = cache('TAG_BRANDS_'.$catId);
		if($cacheData)return $cacheData;
        $where = [];
        $where['r.dataSrc'] = 2;
        $where['b.dataFlag'] = 1; 
        $where['r.dataType'] = 0;
	    $where['r.goodsCatId'] = $catId;
	    $brands = Db::name('brands')->alias('b')->join('__RECOMMENDS__ r','b.brandId=r.dataId')
	                   ->where($where)->field('b.brandId,b.brandImg,b.brandName,r.goodsCatId catId')
	                   ->order('r.dataSort asc')->limit($num)->select();
        //为空的话就取分类关联的
        if(empty($brands)){
	         $brands = Db::name('goods_cats')->alias('gc')
					   ->join('__CAT_BRANDS__ gcb','gc.catId=gcb.catId','inner')
					   ->join('__BRANDS__ b','gcb.brandId=b.brandId')
					   ->field('b.brandId,b.brandImg,b.brandName,gcb.catId')
					   ->where('gc.catId',$catId)
					   ->limit($num)
					   ->select();
		}
        cache('TAG_BRANDS_'.$catId,$brands,$cache);
        return $brands;
	}

	/**
	* 获取分类下的店铺
	*/
	public function listShop($catId,$num,$cache = 0){
		$cacheData = cache('TAG_SHOPS_'.$catId);
		if($cacheData)return $cacheData;
        $where = [];
        $where['r.dataSrc'] = 1;
        $where['b.dataFlag'] = 1; 
        $where['r.dataType'] = 0;
	    $where['r.goodsCatId'] = $catId;
	    $shops = Db::name('shops')->alias('b')->join('__RECOMMENDS__ r','b.shopId=r.dataId')
	                   ->where($where)->field('b.shopId,b.shopImg,b.shopName,r.goodsCatId catId')
	                   ->order('r.dataSort asc')->limit($num)->select();
        //为空的话就取分类关联的
        if(empty($shops)){
	         $shops = Db::name('goods_cats')->alias('gc')
					   ->join('__CAT_SHOPS__ gcb','gc.catId=gcb.catId','inner')
					   ->join('__SHOPS__ b','gcb.shopId=b.shopId')
					   ->field('b.shopId,b.shopImg,b.shopName,gcb.catId')
					   ->where('gc.catId',$catId)
					   ->limit($num)
					   ->select();
		}
        cache('TAG_SHOPS_'.$catId,$shops,$cache);
        return $shops;
	}
}
