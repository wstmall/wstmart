<?php
namespace wstmart\admin\model;
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
 * 菜单业务处理
 */
class Menus extends Base{
	protected $insert = ['dataFlag'=>1]; 
	/**
	 * 获取菜单列表
	 */
	public function listQuery($parentId = -1){
		if($parentId==-1)return ['id'=>0,'name'=>WSTConf('CONF.mallName'),'isParent'=>true,'open'=>true];
		$rs = $this->where(['parentId'=>$parentId,'dataFlag'=>1])->field('menuId id,menuName name')->order('menuSort', 'asc')->select();
		if(count($rs)>0){
			foreach ($rs as $key =>$v){
				$rs[$key]['isParent'] = true;
			}
		};
		return $rs;
	}
	/**
	 * 获取菜单
	 */
	public function getById($id){
		return $this->get(['dataFlag'=>1,'menuId'=>$id]);
	}
	
	/**
	 * 新增菜单
	 */
	public function add(){
		$result = $this->validate('Menus.add')->save(input('post.'));
        if(false !== $result){
        	return WSTReturn("新增成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑菜单
	 */
	public function edit(){
		$menuId = input('post.menuId/d');
	    $result = $this->validate('Menus.edit')->allowField(['menuName','menuSort'])->save(input('post.'),['menuId'=>$menuId]);
        if(false !== $result){
        	return WSTReturn("编辑成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	 * 删除菜单
	 */
	public function del(){
	    $menuId = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
	    $result = $this->update($data,['menuId'=>$menuId]);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
	/**
	 * 获取用户菜单
	 */
	public function getMenus(){
		$STAFF = session('WST_STAFF');
		return $this->where(['parentId'=>0,'dataFlag'=>1,'menuId'=>['in',$STAFF['menuIds']]])->field('menuId,menuName')->order('menuSort', 'asc')->select();
	}
	
	/**
	 * 获取子菜单
	 */
	public function getSubMenus($parentId){
		//用户权限判断
		$STAFF = session('WST_STAFF');
		$allowMenus = [];
		$rs2 = $this->where(['parentId'=>$parentId,'dataFlag'=>1,'menuId'=>['in',$STAFF['menuIds']]])->field('menuId,menuName')->order('menuSort', 'asc')->select();
		foreach ($rs2 as $key2 =>$v2){
			if(!in_array($v2['menuId'],$STAFF['menuIds']))continue;
			$rs3 = Db::name('menus')->alias('m')->join('__PRIVILEGES__ p','m.menuId= p.menuId and isMenuPrivilege=1 and p.dataFlag=1','inner')
			->where(['parentId'=>$v2['menuId'],'m.dataFlag'=>1,'m.menuId'=>['in',$STAFF['menuIds']]])
			->field('m.menuId,m.menuName,privilegeUrl')
			->order('menuSort', 'asc')
			->select();
			if(!empty($rs3))$rs2[$key2]['list'] = $rs3;
		}
		return $rs2;
	}
}
