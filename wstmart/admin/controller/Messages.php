<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Messages as M;
use wstmart\admin\model\ServicesJson as Services_JSON;
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
 * 商城消息控制器
 */
class Messages extends Base{
	
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
    * 查找用户
    */
    public function userQuery(){
        $m = model('users');
        return $m->getByName(input('post.loginName'));
    }
    /**
    * 发送消息
    */
    public function add(){
        $m = new M();
        return $m->add();
    }
    /**
     * 删除
     */
    public function del(){
        $m = new M();
        return $m->del();
    }
    /**
    * 查看完整消息
    */
    public function showFullMsg(){
        $m = new M();
        $rs = $m->getById(Input("id/d",0));
        return $this->fetch('msg', ['data'=>$rs]);

    }




























}
