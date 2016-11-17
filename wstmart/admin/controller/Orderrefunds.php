<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\OrderRefunds as M;
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
 * 退款订单控制器
 */
class Orderrefunds extends Base{
    /**
     * 退款列表
     */
    public function refund(){
    	$areaList = model('areas')->listQuery(0); 
    	$this->assign("areaList",$areaList);
    	return $this->fetch("list");
    }
    public function refundPageQuery(){
        $m = new M();
        return $m->refundPageQuery();
    }
    /**
     * 跳去退款界面
     */
    public function toRefund(){
    	$m = new M();
    	$object = $m->getInfoByRefund();
    	$this->assign("object",$object);
    	return $this->fetch("box_refund");
    }
    /**
     * 退款
     */
    public function orderRefund(){
    	$m = new M();
        return $m->orderRefund();
    }
}
