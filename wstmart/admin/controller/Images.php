<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Images as M;
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
 * 图片空间控制器
 */
class Images extends Base{
	/**
	 * 进入主页面
	 */
    public function index(){
    	return $this->fetch();
    }
    /**
     * 获取概况  
     * 后台商城消息 编辑器中的图片只记录上传图片容量  删除相关数据时无法标记图片为已删除状态
     */
    public function summary(){
    	$m = new M();
    	$data = $m->summary();
        return WSTReturn("", 1,$data);
    }
    /**
	 * 进入列表页面
	 */
    public function lists(){
    	$datas = model('Datas')->listQuery(3);
    	$this->assign('datas',$datas);
    	$this->assign('keyword',input('get.keyword'));
    	return $this->fetch('list');
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        return $m->pageQuery();
    }
    /**
     * 检测图片信息
     */
    public function checkImages(){
    	$imgPath = input('get.imgPath');
    	$m = WSTConf('CONF.wstMobileImgSuffix');
	    $imgPath = str_replace($m.'.','.',$imgPath);
	    $imgPath = str_replace($m.'_thumb.','.',$imgPath);
	    $imgPath = str_replace('_thumb.','.',$imgPath);
	    $imgPath_thumb = str_replace('.','_thumb.',$imgPath);
	    $mimg = '';
	    $mimg_thumb = '';
	    if($m!=''){
		    $mimg = str_replace('.',$m.'.',$imgPath);
		    $mimg_thumb = str_replace('.',$m.'_thumb.',$imgPath);
	    }
    	$data['imgpath']=$imgPath;
    	$data['img']=file_exists(WSTRootPath()."/".$imgPath)?true:false;
    	$data['thumb']=file_exists(WSTRootPath()."/".$imgPath_thumb)?true:false;
    	$data['thumbpath']=$imgPath_thumb;
    	$data['mimg']=file_exists(WSTRootPath()."/".$mimg)?true:false;
    	$data['mimgpath']=$mimg;
    	$data['mthumb']=file_exists(WSTRootPath()."/".$mimg_thumb)?true:false;
    	$data['mthumbpath']=$mimg_thumb;
    	return $this->fetch('view',$data);
    }
    /**
     * 删除
     */
    public function del(){
        $m = new M();
        return $m->del();
    }  
}
