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
 * 商城配置业务处理
 */
use think\Db;
class Styles extends Base{
	/**
	 * 获取分类
	 */
	public function getCats(){
		return $this->distinct(true)->field('styleSys')->select();
	}
	/**
	 * 获取风格列表
	 */
	public function listQuery(){
		$styleSys = input('styleSys');
		$rs = $this->where('styleSys',$styleSys)->select();
		return ['sys'=>$styleSys,'list'=>$rs];
	}
	
    /**
	 * 编辑
	 */
	public function changeStyle(){
		 $id = (int)input('post.id');
		 $object = $this->get($id);
		 Db::startTrans();
         try{
		     $rs = $this->where('styleSys',$object['styleSys'])->update(['isUse'=>0]);
		     if(false !== $rs){
		         $object->isUse = 1;
		         $object->save();
		         cache('WST_CONF',null);
		         Db::commit();
		         return WSTReturn('操作成功',1);
		     }
		 }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('操作失败');
        }
         
    }
	
}
