<?php
namespace wstmart\home\model;
use think\Db;
use wstmart\home\model\Shops;
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
 * 运费管理类
 */
class ShopFreights extends Base{
	/**
	 *  运费列表
	 */
	public function listProvince(){
		$shopId = session('WST_USER.shopId');
		$listCity = Db::name('areas')->where(['isShow'=>1,'dataFlag'=>1,'areaType'=>0])->field('areaId,areaName')->order('areaKey desc')->select();
		for ($i = 0; $i < count($listCity); $i++) {
			$parentId = $listCity[$i]["areaId"];
			$listPro = Db::name('areas')->alias('a')
			->join('__SHOP_FREIGHTS__ s','a.areaId= s.areaId2 and s.shopId='.$shopId,'left')
			->where(['a.isShow'=>1,'a.dataFlag'=>1,'a.areaType'=>1,'a.parentId'=>$parentId])
			->field('a.areaId,a.areaName,a.parentId,s.freightId,s.freight')
			->order('a.areaKey desc')
			->select();
			$listCity[$i]['listProvince'] = $listPro;
		}	
		return $listCity;
	}
	
	/**
	 * 编辑
	 */
	public function edit(){
		$shopId = session('WST_USER.shopId');
		$info = input("post.");
		$areas = Db::name('areas')->where('isShow=1 AND dataFlag = 1 AND areaType=1')->field('areaId')->select();
		Db::startTrans();
		if(count($areas)==0)return WSTReturn('无效的城市');
		try{
		   $dataList = [];
           foreach ($areas as $key => $v) {
           	   $m = model('ShopFreights')->where(['shopId'=>$shopId,'areaId2'=>$v['areaId']])->find();
           	   $freight = ((int)input('post.'.$v['areaId'])>0)?(int)input('post.'.$v['areaId']):0;
           	   if($m){
           	   	   $m->freight = $freight;
           	   	   $m->save();
           	   }else{
           	   	   $data = [];
                   $data['shopId'] = $shopId;
                   $data['areaId2'] = $v['areaId'];
                   $data['freight'] = $freight;
                   $data['createTime'] = date('Y-m-d H:i:s');
                   $dataList[] = $data;
           	   }
           }
           if(count($dataList)>0)model('ShopFreights')->saveAll($dataList);
		   Db::commit();
		   return WSTReturn("操作成功", 1);
		}catch (\Exception $e) {
			Db::rollback();
			return WSTReturn('操作失败',-1);
		}
	}
}
