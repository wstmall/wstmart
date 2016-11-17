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
 * 地区业务处理
 */
class Areas extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		$parentId = input('get.parentId/d',0);
		return $this->where(['dataFlag'=>1,'parentId'=>$parentId])->order('areaId desc')->paginate(input('post.pagesize/d'));
	}
	
	/**
	 * 获取指定对象
	 */
	public function getById($id){
		return $this->get(['dataFlag'=>1,'areaId'=>$id]);
	}
	
	/**
	 * 获取地区
	 */
	public function getFieldsById($id,$fileds){
		return $this->where(['dataFlag'=>1,'areaId'=>$id])->field($fileds)->find();
	}
	
	/**
	 * 显示是否显示/隐藏
	 */
	public function editiIsShow(){
		//获取子集
		$ids = array();
		$ids[] = input('post.id/d',0);
		$ids = $this->getChild($ids,$ids);
		$isShow = input('post.isShow/d',0)?0:1;
		$result = $this->where("areaId in(".implode(',',$ids).")")->update(['isShow' => $isShow]);
		if(false !== $result){
			return WSTReturn("操作成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 * 迭代获取下级
	 */
	public function getChild($ids = array(),$pids = array()){
		$result = $this->where("dataFlag=1 and parentId in(".implode(',',$pids).")")->select();
		if(count($result)>0){
			$cids = array();
			foreach ($result as $key =>$v){
				$cids[] = $v['areaId'];
			}
			$ids = array_merge($ids,$cids);
			return $this->getChild($ids,$cids);
		}else{
			return $ids;
		}
	}
	
    /**
	 * 根据子分类获取其父级分类
	 */
	public function getParentIs($id,$data = array()){
		$data[] = $id;
		$parentId = $this->where('areaId',$id)->value('parentId');
		if($parentId==0){
			krsort($data);
			return $data;
		}else{
			return $this->getParentIs($parentId, $data);
		}
	}

	/**
	 * 获取自己以及父级的地区名称
	 */
	public function getParentNames($id,$data = array()){
		$areas = $this->where('areaId',$id)->field('parentId,areaName')->find();
		$data[] = $areas['areaName'];
		if((int)$areas['parentId']==0){
			krsort($data);
			return $data;
		}else{
			return $this->getParentNames((int)$areas['parentId'], $data);
		}
	}

	
	/**
	 * 排序字母
	 */
	public function letterObtain(){
		$areaName =  input('code');
		if($areaName =='')return WSTReturn("", 1);
		$areaName = WSTGetFirstCharter($areaName);
		if($areaName){
			return WSTReturn($areaName, 1);
		}else{
			return WSTReturn("", 1);
		}
	}
	
	/**
	 * 新增
	 */
	public function add(){
		$areaType = 0;
		$parentId = input('post.parentId/d',0);
		if($parentId>0){
			$prs = $this->getFieldsById($parentId,['areaType']);
			$areaType = $prs['areaType']+1;
		}
		$data = input('post.');
		WSTUnset($data,'areaId,dataFlag');
		$data['areaType'] = $areaType;
		$data['createTime'] = date('Y-m-d H:i:s');
		$result = $this->validate('Areas.add')->allowField(true)->save($data);
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
		$areaId = input('post.areaId/d');
		$result = $this->validate('Areas.edit')->allowField(['areaName','isShow','areaSort','areaKey'])->save(input('post.'),['areaId'=>$areaId]);
		$ids = array();
		$ids[] = $areaId;
		$ids = $this->getChild($ids,$ids);
		$this->where("areaId in(".implode(',',$ids).")")->update(['isShow' => input('post.')['isShow']]);
		if(false !== $result){
			return WSTReturn("修改成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 * 删除
	 */
	public function del(){
		$ids = array();
		$ids[] = input('post.id/d');
		$ids = $this->getChild($ids,$ids);
		$data = [];
		$data['dataFlag'] = -1;
		$result = $this->where("areaId in(".implode(',',$ids).")")->update($data);
		if(false !== $result){
			return WSTReturn("删除成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 *  获取地区列表
	 */
	public function listQuery($parentId){
		return $this->where(['dataFlag'=>1,'parentId'=>$parentId,'isShow'=>1])->field('areaId,areaName,parentId')->order('areaSort desc')->select();
	}
}