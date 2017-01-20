<?php
namespace wstmart\home\model;
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
 * 门店配置类
 */
use think\Db;
class ShopConfigs extends Base{
    /**
    * 店铺设置
    */
     public function getShopCfg($id){
        $rs = $this->where("shopId=".$id)->find();
        if($rs != ''){
            //图片
            $rs['shopAds'] = ($rs['shopAds']!='')?explode(',',$rs['shopAds']):null;
            //图片的广告地址
            $rs['shopAdsUrl'] = ($rs['shopAdsUrl']!='')?explode(',',$rs['shopAdsUrl']):null;
            return $rs;
        }
     }

     /**
      * 修改店铺设置
      */
     public function editShopCfg($shopId){
        $data = input('post.');
        //加载商店信息
        Db::startTrans();
		try{
	        $shopcg = $this->where('shopId='.$shopId)->find(); 
	        $scdata = array();
	        $scdata["shopId"] =  $shopId;
	        $scdata["shopKeywords"] =  Input("shopKeywords");
	        $scdata["shopBanner"] =  Input("shopBanner");
	        $scdata["shopDesc"] =  Input("shopDesc");
	        $scdata["shopAds"] =  Input("shopAds");
	        $scdata["shopAdsUrl"] =  Input("shopAdsUrl");
	        $scdata["shopHotWords"] =  Input("shopHotWords");
	        WSTUseImages(0, $shopcg['configId'], $scdata['shopBanner'],'shop_configs','shopBanner');
	        WSTUseImages(0, $shopcg['configId'], $scdata['shopAds'],'shop_configs','shopAds');
	        $rs = $this->where("shopId=".$shopId)->update($scdata);	
	        if($rs!==false){
	        	Db::commit();
	            return WSTReturn('操作成功',1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('操作失败',-1);
     }
     /**
      * 获取商城搜索关键字
      */
     public function searchShopkey($shopId){
     	$rs = $this->where('shopId='.$shopId)->field('configId,shopHotWords')->find();
     	$keys = [];
     	if($rs['shopHotWords']!='')$keys = explode(',',$rs['shopHotWords']);
     	return $keys;
     }
}
