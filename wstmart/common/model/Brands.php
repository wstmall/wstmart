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
 * 品牌业务处理类
 */
class Brands extends Base{
	/**
	 * 获取品牌列表
	 */
	public function pageQuery($pagesize){
		$id = (int)input('id');
		$where['b.dataFlag']=1;
		if($id>1){
			$where['gcb.catId']=$id;
		}
		$rs = $this->alias('b')
				   ->join('__CAT_BRANDS__ gcb','gcb.brandId=b.brandId','left')
				   ->where($where)
				   ->field('b.brandId,brandName,brandImg,gcb.catId')
				   ->paginate($pagesize)->toArray();
		return $rs;
	}
	/**
	 * 获取品牌列表
	 */
	public function listQuery($catId){
		$rs = Db::name('cat_brands')->alias('l')->join('__BRANDS__ b','b.brandId=l.brandId and b.dataFlag=1 and l.catId='.$catId)
		          ->field('b.brandId,b.brandName')->select();
		return $rs;
	}
}
