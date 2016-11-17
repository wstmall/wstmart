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
 * 商城消息控制器
 */
class Messages extends Base{
    /**
    * 查看商城消息
    */
	public function index(){
		return $this->fetch('users/messages/list');
	}
    /**
    * 获取数据
    */
    public function pageQuery(){
        $data = model('Messages')->pageQuery();
        return WSTReturn("", 1,$data);
    }
    /**
    * 查看完整商城消息
    */
    public function showMsg(){
        $data = model('Messages')->getById();
        return $this->fetch('users/messages/show',['data'=>$data]);
    }
	
    /**
    * 删除
    */
    public function del(){
    	$m = model('Home/Messages');
        $rs = $m->del();
        return $rs;
    }
    /**
    * 批量删除
    */
    public function batchDel(){
        $m = model('Home/Messages');
        $rs = $m->batchDel();
        return $rs;
    }
    /**
    * 标记为已读
    */
    public function batchRead(){
        $m = model('Home/Messages');
        $rs = $m->batchRead();
        return $rs;
    }


}
