<?php 
namespace wstmart\home\validate;
use think\Validate;
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
 * 商品验证器
 */
class Goods extends Validate{
	protected $rule = [
        ['goodsName'  ,'require|max:300','请输入商品编号|商品名称不能超过100个字符'],
        ['goodsImg'  ,'require','请上传商品图片'],
        ['goodsSn'  ,'checkGoodsSn:1','请输入商品编号'],
        ['productNo'  ,'checkProductNo:1','请输入商品货号'],
        ['marketPrice'  ,'require|float','请输入市场价格|市场价格只能为数字'],
        ['shopPrice'  ,'require|float','请输入店铺价格|店铺价格只能为数字'],
        ['goodsUnit'  ,'require','请输入商品单位'],
        ['isSale'  ,'in:,0,1','无效的上架状态'],
        ['isRecom'  ,'in:,0,1','无效的推荐状态'],
        ['isBest'  ,'in:,0,1','无效的精品状态'],
        ['isNew'  ,'in:,0,1','无效的新品状态'],
        ['isHot'  ,'in:,0,1','无效的热销状态'],
        ['goodsCatId'  ,'require','请选择完整商品分类'],
        ['goodsDesc','require','请输入商品描述'],
        ['specsIds','checkSpecsIds:1','请填写完整商品规格信息']
    ];
    /**
     * 检测商品编号
     */
    protected function checkGoodsSn($value){
    	$goodsId = Input('post.goodsId/d',0);
    	$key = Input('post.goodsSn');
    	if($key=='')return '请输入商品编号';
    	$isChk = model('Goods')->checkExistGoodsKey('goodsSn',$key,$goodsId);
    	if($isChk)return '对不起，该商品编号已存在';
    	return true;
    }
    /**
     * 检测商品货号
     */
    protected function checkProductNo($value){
    	$goodsId = Input('post.goodsId/d',0);
    	$key = Input('post.productNo');
    	if($key=='')return '请输入商品货号';
    	$isChk = model('Goods')->checkExistGoodsKey('productNo',$key,$goodsId);
    	if($isChk)return '对不起，该商品货号已存在';
    	return true;
    }
    /**
     * 检测商品规格是否填写完整
     */
    public function checkSpecsIds(){
    	$specsIds = input('post.specsIds');
    	if($specsIds!=''){
	    	$str = explode(',',$specsIds);
	    	$specsIds = [];
	    	foreach ($str as $v){
	    		$vs = explode('-',$v);
	    		foreach ($vs as $vv){
	    		   if(!in_array($vv,$specsIds))$specsIds[] = $vv;
	    		}
	    	}
    		//检测规格名称是否填写完整
    		foreach ($specsIds as $v){
    			if(input('post.specName_'.$v)=='')return '请填写完整商品规格值sn'.'specName_'.$v;
    		}
    		//检测销售规格是否完整	
    		foreach ($str as $v){
    			if(input('post.productNo_'.$v)=='')return '请填写完整商品销售规格信息1';
    			if(input('post.marketPrice_'.$v)=='')return '请填写完整商品销售规格信息2';
    			if(input('post.specPrice_'.$v)=='')return '请填写完整商品销售规格信息3';
    			if(input('post.specStock_'.$v)=='')return '请填写完整商品销售规格信息4';
    			if(input('post.warnStock_'.$v)=='')return '请填写完整商品销售规格信息5';
    		}
    		if(input('post.defaultSpec')=='')return '请选择推荐规格';
    	}
    	return true;
    }
}