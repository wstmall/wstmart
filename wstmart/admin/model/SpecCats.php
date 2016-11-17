<?php
namespace wstmart\admin\model;
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
 * 规格分类业务处理
 */
class SpecCats extends Base{
	
	/**
	 * 新增
	 */
	public function add(){
		$isExistAllowImg = false;
		$msg = '';
		$data = input('post.');
		if($data['isAllowImg']==1){
			if($this->checkExistAllowImg((int)$data['goodsCatId'],0)){
				return WSTReturn("同一分类下已存在允许上传图片规格类型，请先修改之后再新增");
			}
		}
		$data['createTime'] = date('Y-m-d H:i:s');
		$data["dataFlag"] = 1;
		$goodsCats = model('GoodsCats')->getParentIs($data['goodsCatId']);
		if(!empty($goodsCats))$data['goodsCatPath'] = implode('_',$goodsCats)."_";
		$result = $this->validate('SpecCats.add')->allowField(['catName','isShow','isAllowImg','goodsCatPath','goodsCatId','dataFlag','createTime'])->save($data);
        if(false !== $result){
        	return WSTReturn("新增成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	 * 检测是否存在统一分类下的上传图片分类
	 */
	public function checkExistAllowImg($goodsCatId,$catId){
		$dbo = $this->where(['goodsCatId'=>$goodsCatId,'dataFlag'=>1,'isAllowImg'=>1]);
		if($catId>0)$dbo->where('catId','<>',$catId);
		$rs = $dbo->count();
		if($rs>0)return true;
		return false;
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$catId = input('post.catId/d');
		$data = input('post.');
	    if($data['isAllowImg']==1){
			if($this->checkExistAllowImg((int)$data['goodsCatId'],$catId)){
				return WSTReturn("同一分类下已存在允许上传图片规格类型，请先修改之后再保存");
			}
		}
		$goodsCats = model('GoodsCats')->getParentIs($data['goodsCatId']);
		if(!empty($goodsCats))$data['goodsCatPath'] = implode('_',$goodsCats)."_";
	    $result = $this->validate('SpecCats.edit')->allowField(['catName','goodsCatPath','goodsCatId','isShow','isAllowImg'])->save($data,['catId'=>$catId,"dataFlag"=>1]);
        if(false !== $result){
        	return WSTReturn("编辑成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	 * 删除
	 */
    public function del(){
	    $catId = input('post.catId/d');
	    $data["dataFlag"] = -1;
	  	$result = $this->save($data,['catId'=>$catId]);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
	/**
	 * 显示隐藏
	 */
	public function setToggle(){
		$catId = input('post.catId/d');
		$isShow = input('post.isShow/d');
		$result = $this->where(['catId'=>$catId,"dataFlag"=>1])->setField("isShow", $isShow);
		if(false !== $result){
			return WSTReturn("设置成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 * 
	 * 根据ID获取
	 */
	public function getById($catId){
		$obj = null;
		if($catId>0){
			$obj = $this->get(['catId'=>$catId,"dataFlag"=>1]);
		}else{
			$obj = self::getEModel("spec_cats");
		}
		return $obj;
	}
	
	
	/**
	 * 分页
	 */
	public function pageQuery(){
		$keyName = input('get.keyName');
		$goodsCatPath = input('get.goodsCatPath');
		$dbo = $this->field(true);
		$map = array();
		$map['dataFlag']  = 1;
		if($keyName!="")$map['catName']  = ["like","%".$keyName."%"];
		if($goodsCatPath!='')$map['goodsCatPath']  = ["like",$goodsCatPath."_%"];
		$dbo = $dbo->field("catName name, catId id, isShow ,isAllowImg")->where($map);
		$page = $dbo->order('catSort asc,catId asc')->paginate(input('pagesize/d'))->toArray();
		if(count($page['Rows'])>0){
			$keyCats = model('GoodsCats')->listKeyAll();
			foreach ($page['Rows'] as $key => $v){
				$goodsCatPath = $page['Rows'][$key]['goodsCatPath'];
				$page['Rows'][$key]['goodsCatNames'] = self::getGoodsCatNames($goodsCatPath,$keyCats);
				$page['Rows'][$key]['children'] = [];
				$page['Rows'][$key]['isextend'] = false;
			}
		}
		return $page;
	}
	
	public function getGoodsCatNames($goodsCatPath, $keyCats){
		$catIds = explode("_",$goodsCatPath);
		$catNames = array();
		for($i=0,$k=count($catIds);$i<$k;$i++){
			if($catIds[$i]=='')continue;
			$catNames[] = $keyCats[$catIds[$i]];
		}
		return implode("→",$catNames);
	}
}
