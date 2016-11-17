<?php
namespace wstmart\home\controller;
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
 * 门店配置控制器
 */
class Shopconfigs extends Base{
    /**
    * 店铺设置
    */
    public function toShopCfg(){
        //获取商品信息
        $m = model('ShopConfigs');
        $this->assign('object',$m->getShopCfg((int)session('WST_USER.shopId')));
        return $this->fetch('shops/shopconfigs/shop_cfg');
    }

    /**
     * 新增/修改 店铺设置
     */
    public function editShopCfg(){
        $shopId = (int)session('WST_USER.shopId');
        $m = model('ShopConfigs');
        if($shopId>0){
            $rs = $m->editShopCfg($shopId);
        }
        return $rs;
    }

}
