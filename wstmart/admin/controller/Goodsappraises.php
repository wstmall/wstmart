<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\GoodsAppraises as M;
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
 * 商品评价控制器
 */
class Goodsappraises extends Base{
	
    public function index(){
        //获取地区
        $area1 = model('areas')->listQuery(0);
        return $this->fetch("list",['area1'=>$area1,]);
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
        $data = $m->getById(input("get.id/d",0));
        if($data['images']!='')
            $data['images'] = explode(',',$data['images']);
        $assign = ['data'=>$data];
        return $this->fetch("edit",$assign);
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

    
}
