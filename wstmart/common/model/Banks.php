<?php
namespace wstmart\common\model;
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
 * 银行业务处理
 */
class Banks extends Base{
	/**
	 * 列表
	 */
	public function listQuery(){
		$data = cache('WST_BANKS');
		if(!$data){
			$data = $this->where('dataFlag',1)->field('bankId,bankName')->select();
			cache('WST_BANKS',$data,31536000);
		}
		return $data;
	}
}
