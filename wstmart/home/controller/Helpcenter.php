<?php
namespace wstmart\home\controller;
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
 */
class Helpcenter extends Base{
	public function index(){
    	return $this->view();
    }
	public function view(){
		//获取左侧列表
		$m = model('home/Articles');
		$list = $m->helpList();
		$data = $m->getHelpById();
		$this->assign('data',$data);
		$this->assign('list',$list);
		//面包屑导航
		$bcNav = [];
		if(!empty($data)){
			$bcNav = $this->bcNav();
		}
		$this->assign('bcNav',$bcNav);
		return $this->fetch('articles/help');
	}
	/**
	*  记录解决情况
	*/
	public function recordSolve(){
		$m = model('home/Articles');
		return $m->recordSolve();
	}
	/**
	* 面包屑导航
	*/
	public function bcNav(){
		$m = model('home/Articles');
		return $m->bcNav();
	}
}