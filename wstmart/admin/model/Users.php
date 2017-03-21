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
 * 会员业务处理
 */
class Users extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		/******************** 查询 ************************/
		$where = [];
		$where['dataFlag'] = 1;
		$lName = input('get.loginName1');
		$phone = input('get.loginPhone');
		$email = input('get.loginEmail');
		$uType = input('get.userType');
		$uStatus = input('get.userStatus1');
		if(!empty($lName))
			$where['loginName'] = ['like',"%$lName%"];
		if(!empty($phone))
			$where['userPhone'] = ['like',"%$phone%"];
		if(!empty($email))
			$where['userEmail'] = ['like',"%$email%"];
		if(is_numeric($uType))
			$where['userType'] = ['=',"$uType"];
		if(is_numeric($uStatus))
			$where['userStatus'] = ['=',"$uStatus"];

		/********************* 取数据 *************************/
		$rs = $this->where($where)
					->field(['userId','loginName','userName','userPhone','userEmail','userScore','createTime','userStatus','lastTime'])
					->order('userId desc')
					->paginate(input('pagesize/d'));
		return $rs;
	}
	public function getById($id){
		return $this->get(['userId'=>$id]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['createTime'] = date('Y-m-d H:i:s');
		$data["loginSecret"] = rand(1000,9999);
    	$data['loginPwd'] = md5($data['loginPwd'].$data['loginSecret']);
    	WSTUnset($data,'userId');
    	Db::startTrans();
		try{
			$result = $this->validate('Users.add')->allowField(true)->save($data);
			$id = $this->userId;
	        if(false !== $result){
	        	WSTUseImages(1, $id, $data['userPhoto']);
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
		$Id = (int)input('post.userId');
		$data = input('post.');
		$u = $this->where('userId',$Id)->field('loginSecret')->find();
		if(empty($u))return WSTReturn('无效的用户');
		//判断是否需要修改密码
		if(empty($data['loginPwd'])){
			unset($data['loginPwd']);
		}else{
    		$data['loginPwd'] = md5($data['loginPwd'].$u['loginSecret']);
		}
		Db::startTrans();
		try{
			if(isset($data['userPhoto'])){
			    WSTUseImages(1, $Id, $data['userPhoto'], 'users', 'userPhoto');
			}
			
			WSTUnset($data,'createTime,userId');
		    $result = $this->allowField(true)->save($data,['userId'=>$Id]);
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
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id');
	    Db::startTrans();
	    try{
		    $data = [];
			$data['dataFlag'] = -1;
		    $result = $this->update($data,['userId'=>$id]);
	        if(false !== $result){
	        	WSTUnuseImage('users','userPhoto',$id);
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
	    }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
	}
	/**
	* 是否启用
	*/
	public function changeUserStatus($id, $status){
		$result = $this->update(['userStatus'=>(int)$status],['userId'=>(int)$id]);
		if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	* 根据用户名查找用户
	*/
	public function getByName($name){
		return $this->field(['userId','loginName'])->where(['loginName'=>['like',"%$name%"]])->select();
	}
	/**
	* 获取所有用户id
	*/
	public function getAllUserId()
	{
		return $this->where('dataFlag',1)->column('userId');
	}
	/**
	* 重置支付密码
	*/
	public function resetPayPwd(){
		$Id = (int)input('post.userId');
		$loginSecret = $this->where('userId',$Id)->value('loginSecret');
		// 重置支付密码为6个6
		$payPwd = md5('666666'.$loginSecret);
		$result = $this->where('userId',$Id)->setField('payPwd',$payPwd);
		if(false !== $result){
        	return WSTReturn("重置成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
}
