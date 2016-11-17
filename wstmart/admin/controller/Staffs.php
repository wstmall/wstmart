<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Staffs as M;
use wstmart\admin\model\Roles as R;
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
 * 职员控制器
 */
class Staffs extends Base{
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
     * 获取
     */
    public function get(){
    	$m = new M();
    	return $m->get((int)Input("post.id"));
    }
    /**
     * 跳去新增界面
     */
    public function toAdd(){
    	$id = (int)Input("get.id",0);
    	$m = new M();
    	$this->assign("object",['staffId'=>0,'workStatus'=>1,'staffStatus'=>1]);
    	$m = new R();
    	$this->assign("roles",$m->listQuery());
    	return $this->fetch("add");
    }
    /**
     * 跳去编辑页面
     */
    public function toEdit(){
    	$id = (int)Input("get.id",0);
    	$m = new M();
    	$rs = $m->getById($id);
    	$this->assign("object",$rs);
    	$m = new R();
    	$this->assign("roles",$m->listQuery());
    	return $this->fetch("edit");
    }
    /**
     * 新增
     */
    public function add(){
    	$m = new M();
    	return $m->add();
    }
    /**
     * 编辑菜单
     */
    public function edit(){
    	$m = new M();
    	return $m->edit();
    }
    /**
     * 删除菜单
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
    /**
     * 检测账号是否重复
     */
    public function checkLoginKey(){
    	$m = new M();
    	return $m->checkLoginKey(input('post.key'));
    }
    /**
     * 编辑自己密码
     */
    public function editMyPass(){
    	$m = new M();
    	return $m->editMyPass((int)session('WST_STAFF.staffId'));
    }
    /**
     * 编辑职员密码
     */
    public function editPass(){
    	$m = new M();
    	return $m->editPass((int)input('post.staffId'));
    }
}
