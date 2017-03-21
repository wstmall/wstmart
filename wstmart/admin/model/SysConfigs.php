<?php
namespace wstmart\admin\model;
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
 * 商城配置业务处理
 */
use think\Db;
class SysConfigs extends Base{
	/**
	 * 获取商城配置
	 */
	public function getSysConfigs(){
		$rs = $this->field('fieldCode,fieldValue')->select();
		$rv = [];
		foreach ($rs as $v){
			$rv[$v['fieldCode']] = $v['fieldValue'];
		}
		return $rv;
	}

	
    /**
	 * 编辑
	 */
	public function edit($isRequire = false){
		$list = $this->field('configId,fieldCode,fieldValue')->select();
		Db::startTrans();
        try{
			foreach ($list as $key =>$v){
				$code = trim($v['fieldCode']);
				if(in_array($code,['wstVersion','wstMd5','wstMobileImgSuffix','mallLicense']))continue;
				$val = Input('post.'.trim($v['fieldCode']));
				if($isRequire && $val=='')continue;
			    //启用图片
				if(substr($val,0,7)=='upload/' && strpos($val,'.')!==false){
					WSTUseImages(1, $v['configId'],$val, 'sys_configs','fieldValue');
				}
				$this->update(['fieldValue'=>$val],['fieldCode'=>$code]);
			}
			Db::commit(); 
			cache('WST_CONF',null);
			return WSTReturn("操作成功", 1);
        }catch (\Exception $e) {
		    Db::rollback();
		}
		return WSTReturn("操作失败", 1);
	}
}
