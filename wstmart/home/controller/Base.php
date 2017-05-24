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
 * 基础控制器
 */
use think\Controller;
class Base extends Controller {
	public function __construct(){
		parent::__construct();
		$this->assign("v",WSTConf('CONF.wstVersion')."_".WSTConf('CONF.wstPCStyleId'));
	}

	protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
    	$style = WSTConf('CONF.wsthomeStyle')?WSTConf('CONF.wsthomeStyle'):'default';
    	$replace['__STYLE__'] = str_replace('/index.php','',\think\Request::instance()->root()).'/wstmart/home/view/'.WSTConf('CONF.wsthomeStyle');
        return $this->view->fetch($style."/".$template, $vars, $replace, $config);
    }

	/**
	 * 上传图片
	 */
	public function uploadPic(){
		return WSTUploadPic(0);
	}
	/**
    * 编辑器上传文件
    */
    public function editorUpload(){
           return WSTEditUpload(0);
    }
	
	/**
	 * 获取验证码
	 */
	public function getVerify(){
		WSTVerify();
	}

}