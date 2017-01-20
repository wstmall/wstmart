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
 * 权限业务处理
 */
class Privileges extends Base{
	/**
	 * 加载指定菜单的权限
	 */
	public function listQuery($parentId){
		$rs = $this->where(['menuId'=>$parentId,'dataFlag'=>1])->order('privilegeId', 'asc')->select();
		return ['Rows'=>$rs];
	}
	/**
	 * 获取指定权限
	 */
    public function getById($id){
		return $this->get(['privilegeId'=>$id,'dataFlag'=>1]);
	}
	
    /**
	 * 新增
	 */
	public function add(){
		$result = $this->validate('Privileges.add')->allowField(true)->save(input('post.'));
        if(false !== $result){
        	cache('WST_LISTEN_URL',null);
        	return WSTReturn("新增成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$id = input('post.id/d');
	    $result = $this->validate('Privileges.edit')->allowField(true)->save(input('post.'),['privilegeId'=>$id]);
        if(false !== $result){
        	cache('WST_LISTEN_URL',null);
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
	    $result = $this->where(['privilegeId'=>$id])->delete();
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
	/**
	 * 检测权限代码是否存在
	 */
	public function checkPrivilegeCode(){
		$code = input('code');
		if($code=='')return WSTReturn("", 1);
		$rs = $this->where(['privilegeCode'=>$code,'dataFlag'=>1])->Count();
		if($rs==0)return WSTReturn("", 1);
		return WSTReturn("该权限代码已存在!", -1);
	}
	
	/**
	 * 加载权限并且标用户的权限
	 */
	public function listQueryByRole($id){
		$mrs = Db::name('menus')->alias('m')->join('__PRIVILEGES__ p','m.menuId= p.menuId and isMenuPrivilege=1 and p.dataFlag=1','left')
			->where(['parentId'=>$id,'m.dataFlag'=>1])
			->field('m.menuId id,m.menuName name,p.privilegeCode,1 as isParent')
			->order('menuSort', 'asc')
			->select();
		$prs = $this->where(['dataFlag'=>1,'menuId'=>$id])->field('privilegeId id,privilegeName name,privilegeCode,0 as isParent')->select();
		if($mrs){
			if($prs){
				foreach ($prs as $v){
					array_unshift($mrs,$v);
				}
			}
		}else{
		    if($prs)$mrs = $prs;
		}
		if(!$mrs)return [];
		$privileges = session("WST_STAFF.grant");
		if(count($privileges)>0){
			foreach ($mrs as $key =>$v){
				if($v['isParent']==1){
				    $mrs[$key]['isParent'] = true;
				    $mrs[$key]['open'] = true;
				}else{
					$mrs[$key]['id'] = 'p'.$v['id'];
				}
			}
		}
		return $mrs;
	}
	/**
	 * 加载全部权限
	 */
	public function getAllPrivileges(){
		return $this->where(['dataFlag'=>1])->field('menuId,privilegeName,privilegeCode,privilegeUrl,otherPrivilegeUrl')->select();
	}
}
