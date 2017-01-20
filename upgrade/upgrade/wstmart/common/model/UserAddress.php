<?php
namespace wstmart\common\model;
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
 * 用户地址
 */
use think\Db;
class UserAddress extends Base{
     /**
      * 获取列表
      */
      public function listQuery($userId){
         $where = ['userId'=>(int)$userId,'dataFlag'=>1];
         $rs = $this->order('isDefault desc, addressId desc')->where($where)->select();
         $areaIds = [];
         $areaMaps = [];
         foreach ($rs as $key => $v){
         	 $tmp = explode('_',$v['areaIdPath']);
         	 foreach ($tmp as $vv){
         		if($vv=='')continue;
         	    if(!in_array($vv,$areaIds))$areaIds[] = $vv;
         	 }
         	 $rs[$key]['areaId2'] = $tmp[1];
         }
         if(!empty($areaIds)){
	         $areas = Db::name('areas')->where(['dataFlag'=>1,'areaId'=>['in',$areaIds]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         foreach ($rs as $key => $v){
	         	 $tmp = explode('_',$v['areaIdPath']);
	         	 $areaNames = [];
		         foreach ($tmp as $vv){
	         		if($vv=='')continue;
	         	    $areaNames[] = $areaMaps[$vv];
	         	 }
	         	 $rs[$key]['areaName'] = implode('',$areaNames);
	         	 $rs[$key]['areaName1'] = $areaMaps[$v['areaId2']];
	         }
         }
         return $rs;
      }
    /**
    *  获取用户信息
    */
    public function getById($id){
    	$rs = $this->get(['addressId'=>$id,'userId'=>(int)session('WST_USER.userId')]);
        if(empty($rs))return [];
        $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$rs['areaIdPath']);
        $rs['areaId2'] = $tmp[1];
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::name('areas')->where(['dataFlag'=>1,'areaId'=>['in',$areaIds]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$rs['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $rs['areaName'] = implode('',$areaNames);
	         }
         }
        return $rs;
    }
    /**
     * 新增
     */
    public function add(){
        $data = input('post.');
        $data['userId'] = (int)session('WST_USER.userId');
        $data['createTime'] = date('Y-m-d H:i:s');
        $areaIds = model('Areas')->getParentIs((int)input('areaId'));
        if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
        $result = $this->validate('UserAddress.add')->allowField(true)->save($data);
        if(false !== $result){
            //修改默认地址
            if((int)input('post.isDefault')==1)
              $this->where("addressId != $this->addressId")->setField('isDefault',0);
            return WSTReturn("新增成功", 1,['addressId'=>$this->addressId]);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }
    /**
     * 编辑资料
     */
    public function edit(){
        $id = (int)input('post.addressId');
        $data = input('post.');
        $areaIds = model('Areas')->getParentIs((int)input('areaId'));
        if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
        $result = $this->validate('UserAddress.edit')->allowField(true)->save($data,['addressId'=>$id,'userId'=>(int)session('WST_USER.userId')]);
        //修改默认地址
        if((int)input('post.isDefault')==1)
          $this->where("addressId != $id")->setField('isDefault',0);
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
        $id = input('post.id/d');
        $data = [];
        $data['dataFlag'] = -1;
        $result = $this->update($data,['addressId'=>$id,'userId'=>(int)session('WST_USER.userId')]);
        if(false !== $result){
            return WSTReturn("删除成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }

    /**
    * 设置为默认地址
    */
    public function setDefault(){
        $id = (int)input('post.id');
        $this->where("addressId != $id")->setField('isDefault',0);
        $rs = $this->where("addressId = $id and userId=".(int)session('WST_USER.userId'))->setField('isDefault',1);
        if(false !== $rs){
            return WSTReturn("设置成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }
    
    /**
     * 获取默认地址
     */
    public function getDefaultAddress(){
    	$userId = (int)session('WST_USER.userId');
    	$where = ['userId'=>$userId,'dataFlag'=>1];
        $rs = $this->where($where)->order('isDefault desc,addressId desc')->find();
        if(empty($rs))return [];
        $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$rs['areaIdPath']);
        $rs['areaId2'] = $tmp[1];
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::name('areas')->where(['dataFlag'=>1,'areaId'=>['in',$areaIds]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$rs['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $rs['areaName'] = implode('',$areaNames);
	         }
         }
         return $rs;
    }
}
