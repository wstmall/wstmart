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
 * 规格业务处理
 */
class Attributes extends Base{
	
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		WSTUnset($data, 'attrId,dataFlag');
		$data['createTime'] = date('Y-m-d H:i:s');
		$data['attrVal'] = str_replace('，',',',$data['attrVal']); 
		$data["dataFlag"] = 1;
		$goodsCats = model('GoodsCats')->getParentIs($data['goodsCatId']);
		krsort($goodsCats);
		if(!empty($goodsCats))$data['goodsCatPath'] = implode('_',$goodsCats)."_";
		$result = $this->validate('Attributes.add')->allowField(true)->save($data);
        if(false !== $result){
        	return WSTReturn("新增成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$attrId = input('post.attrId/d');
		$data = input('post.');
		WSTUnset($data, 'attrId,dataFlag,createTime');
		$data['attrVal'] = str_replace('，',',',$data['attrVal']); 
		$goodsCats = model('GoodsCats')->getParentIs($data['goodsCatId']);
		krsort($goodsCats);
		if(!empty($goodsCats))$data['goodsCatPath'] = implode('_',$goodsCats)."_";
	    $result = $this->validate('Attributes.edit')->allowField(true)->save($data,['attrId'=>$attrId]);
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
	    $attrId = input('post.attrId/d');
	    $data["dataFlag"] = -1;
	  	$result = $this->save($data,['attrId'=>$attrId]);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
	/**
	 * 
	 * 根据ID获取
	 */
	public function getById($attrId){
		$obj = null;
		if($attrId>0){
			$obj = $this->get(['attrId'=>$attrId,'dataFlag'=>1]);
		}else{
			$obj = self::getEModel("attributes");
		}
		return $obj;
	}
	
	/**
	 * 显示隐藏
	 */
	public function setToggle(){
		$attrId = input('post.attrId/d');
		$isShow = input('post.isShow/d');
		$result = $this->where(['attrId'=>$attrId,"dataFlag"=>1])->setField("isShow", $isShow);
		if(false !== $result){
			return WSTReturn("设置成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
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
		$page = $dbo->field(true)->where($map)->paginate(input('pagesize/d'))->toArray();
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
	
	/**
	 * 列表
	 */
	public function listQuery(){
		$catId = input("post.catId/d");
		$rs = $this->field("attrId id, attrId, catId, attrName name,  '' goodsCatNames")->where(["dataFlag"=>1,"catId"=>$catId])->sort('attrSort asc,attrId asc')->select();
		return $rs;
	}
}
