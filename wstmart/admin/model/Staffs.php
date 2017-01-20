<?php
namespace wstmart\admin\model;
use wstmart\admin\model\Roles;
use wstmart\admin\model\LogStaffLogins;
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
 * 职员业务处理
 */
class Staffs extends Base{
	
	/**
	 * 判断用户登录帐号密码
	 */
	public function checkLogin(){
		$loginName = input("post.loginName");
		$loginPwd = input("post.loginPwd");
		$code = input("post.verifyCode");
		if(!WSTVerifyCheck($code)){
			return WSTReturn('验证码错误!');
		}
		$staff = $this->where(['loginName'=>$loginName,'staffStatus'=>1,'dataFlag'=>1])->find();
		if(empty($staff))return WSTReturn('账号或密码错误!');
		if($staff['loginPwd']==md5($loginPwd.$staff['secretKey'])){
	 		$staff->lastTime = date('Y-m-d H:i:s');
	 		$staff->lastIP = request()->ip();
	 		$staff->save();
	 		//记录登录日志
		 	LogStaffLogins::create([
		 	     'staffId'=>$staff['staffId'],
		 	     'loginTime'=> date('Y-m-d H:i:s'),
		 	     'loginIp'=>request()->ip()
		 	]);
	 		//获取角色权限
	 		$role = Roles::get(['dataFlag'=>1,'roleId'=>$staff['staffRoleId']]);
	 		$staff['roleName'] = $role['roleName'];
	 		if($staff['staffId']==1){
	 			$staff['privileges'] = Db::name('privileges')->where(['dataFlag'=>1])->column('privilegeCode');
	 			$staff['menuIds'] = Db::name('menus')->where('dataFlag',1)->column('menuId');
	 		}else{
		 		$staff['privileges'] = explode(',',$role['privileges']);
		 		$staff['menuIds'] = [];
		 		//获取管理员拥有的菜单
		 		if(!empty($staff['privileges'])){
		 		     $menus = Db::name('menus')->alias('m')->join('__PRIVILEGES__ p','m.menuId=p.menuId and p.dataFlag=1','inner')
		 		                ->where(['p.privilegeCode'=>['in',$staff['privileges']]])->field('m.menuId')->select();
		 		     $menuIds = [];
		 		     if(!empty($menus)){
		 		     	foreach ($menus as $key => $v){
		 		     		$menuIds[] = $v['menuId'];
		 		     	}
		 		     	$staff['menuIds'] = $menuIds;
		 		     }
		 		}
	 		}
	 		session("WST_STAFF",$staff);
			return WSTReturn("",1,$staff);
		}
		return WSTReturn('账号或密码错误!');
	}
	
    /**
	 * 分页
	 */
	public function pageQuery(){
		$key = input('get.key');
		$where = [];
		$where['s.dataFlag'] = 1;
		if($key!='')$where['loginName|staffName|staffNo'] = ['like','%'.$key.'%'];
		return Db::name('staffs')->alias('s')->join('__ROLES__ r','s.staffRoleId=r.roleId and r.dataFlag=1','left')
		       ->where($where)->field('staffId,staffName,loginName,workStatus,staffNo,lastTime,lastIP,roleName')
		       ->order('staffId', 'desc')->paginate(input('pagesize/d'));
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
		    $result = $this->update($data,['staffId'=>$id]);
	        if(false !== $result){
	        	WSTUnuseImage('staffs','staffPhoto',$id);
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('删除失败',-1);
        }
	}
	
	/**
	 * 获取角色权限
	 */
	public function getById($id){
		return $this->get(['dataFlag'=>1,'staffId'=>$id]);
	}
	
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['secretKey'] = rand(1000,9999);
		$data["loginPwd"] = md5(input("post.loginPwd").$data["secretKey"]);
		$data["staffFlag"] = 1;
		$data["createTime"] = date('Y-m-d H:i:s');
		Db::startTrans();
		try{
		   $result = $this->validate('Staffs.add')->allowField(true)->save($data);
		   if(false !== $result){
		      WSTUseImages(1, $this->staffId,$data['staffPhoto']);
		      Db::commit();
              return WSTReturn("新增成功", 1);
		   }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败',-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$id = input('post.staffId/d');
		$data = input('post.');
		WSTUnset($data, 'staffId,loginPwd,secretKey,dataFlag,createTime,lastTime,lastIP');
		Db::startTrans();
		try{
			WSTUseImages(1, $id,$data['staffPhoto'],'staffs','staffPhoto');
		    $result = $this->validate('Staffs.edit')->allowField(true)->save($data,['staffId'=>$id]);
	        if(false !== $result){
		        Db::commit();
	        	return WSTReturn("编辑成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
	}
	
	/**
	 * 检测账号是否重复
	 */
	public function checkLoginKey($key){
		$rs = $this->where(['loginName'=>$key,'dataFlag'=>1])->count();
		return ($rs==0)?WSTReturn('该账号可用', 1):WSTReturn("对不起，该账号已存在");
	}
	/**
	 * 修改自己密码
	 */
	public function editMyPass($staffId){
		if(input("post.newPass")=='')WSTReturn("密码不能为空");
		$staff = $this->where('staffId',$staffId)->field('secretKey,loginPwd')->find();
		if(empty($staff))return WSTReturn("修改失败");
		$srcPass = md5(input("post.srcPass").$staff["secretKey"]);
		if($srcPass!=$staff['loginPwd'])return WSTReturn("原密码错误");
		$staff->loginPwd = md5(input("post.newPass").$staff["secretKey"]);
		$result = $staff->save();
        if(false !== $result){
        	return WSTReturn("修改成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
   /**
	 * 修改职员密码
	 */
	public function editPass($staffId){
		if(input("post.newPass")=='')WSTReturn("密码不能为空");
		$staff = $this->where('staffId',$staffId)->field('secretKey')->find();
		if(empty($staff))return WSTReturn("修改失败");
		$staff->loginPwd = md5(input("post.newPass").$staff["secretKey"]);
		$result = $staff->save();
        if(false !== $result){
        	return WSTReturn("修改成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
}
