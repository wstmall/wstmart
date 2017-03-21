<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Users as M;
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
 * 会员控制器
 */
class Users extends Base{
	
    public function index(){
    	return $this->fetch("list");
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        return $m->pageQuery();
    }
    /**
     * 跳去编辑页面
     */
    public function toEdit(){
        $m = new M();
        $data = $this->get();
        $assign = ['data'=>$data];
        return $this->fetch("edit",$assign);
    }
    /*
    * 获取数据
    */
    public function get(){
        $m = new M();
        return $m->getById((int)Input("id"));
    }
    /**
     * 新增
     */
    public function add(){
        $m = new M();
        return $m->add();
    }
    /**
    * 修改
    */
    public function edit(){
        $m = new M();
        return $m->edit();
    }
    /**
     * 删除
     */
    public function del(){
        $m = new M();
        return $m->del();
    }
    /**********************************************************************************************
      *                                             账号管理                                                                                                                              *
      **********************************************************************************************/
    /**
    * 账号管理页面
    */
    public function accountIndex(){
        return $this->fetch("account_list");
    }
    /**
     * 判断账号是否存在
     */
    public function checkLoginKey(){
    	$rs = WSTCheckLoginKey(Input('post.loginName'),Input('post.userId/d',0));
    	if($rs['status']==1){
    		return ['ok'=>$rs['msg']];
    	}else{
    		return ['error'=>$rs['msg']];
    	}
    }
    /**
    * 是否启用
    */
    public function changeUserStatus($id, $status){
        $m = new M();
        return $m->changeUserStatus($id, $status);
    }
    public function editAccount(){
        $m = new M();
        return $m->edit();
    }
    /**
    * 获取所有用户id
    */
    public function getAllUserId()
    {
        $m = new M();
        return $m->getAllUserId();
    }
    /**
    * 重置支付密码
    */
    public function resetPayPwd(){
        $m = new M();
        return $m->resetPayPwd();
    }
}
