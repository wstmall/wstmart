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
 * 图片空间业务处理
 */
use think\Db;
class Images extends Base{
	/**
	 * 获取图片空间概况
	 */
	public function summary(){
		$rs = Db::name('images')->where(['dataFlag'=>1])->field('fromTable,isUse,sum(imgSize) imgSize')->group('fromTable,isUse')
		        ->order('fromTable asc')->select();
		//获取目录名称
		$rs2 = Db::name('datas')->where(['catId'=>3])->field('dataName,dataVal')->select();
		$imagesMap = [];
		foreach ($rs2 as $key =>$v){
			$imagesMap[$v['dataVal']] = $v['dataName'];
		}
	    $images = [];
		foreach ($rs as $key =>$v){
			if(!isset($images[$v['fromTable']]))$images[$v['fromTable']] = ['directory'=>'','data'=>['0'=>0,'1'=>0]];
			if(isset($imagesMap[$v['fromTable']]))$images[$v['fromTable']]['directory'] = $imagesMap[$v['fromTable']];
		    $images[$v['fromTable']]['data'][$v['isUse']] = round($v['imgSize']/1024/1024,2);
		}
		$maxSize = 0;
		foreach ($images as $key =>$v){
			$size = (float)$v['data']['0']+(float)$v['data']['1'];
			if($maxSize<$size)$maxSize = $size;
		}
		$images['_WSTSummary_'] = $maxSize;
		return $images;
	}
	/**
	 * 获取记录
	 */
	public function pageQuery(){
		$key = input('keyword');
		$isUse = (int)input('isUse');
		$where = ['fromTable'=>$key,'a.dataFlag'=>1];
		if($isUse !=-1)$where['isUse'] = $isUse;
		$page = $this->alias('a')->join('__USERS__ u','a.ownId=u.userId and fromType=0','left')
		            ->join('__SHOPS__ s','s.userId=u.userId','left')
		            ->join('__STAFFS__ sf','sf.staffId=a.ownId','left')
		            ->where($where)->field('a.imgId,u.loginName,u.userType,fromType,sf.loginName loginName2,s.shopName,imgPath,imgSize,isUse,a.createTime')
		            ->order('a.imgId desc')->paginate(input('post.pagesize/d'))->toArray();
		foreach ($page['Rows'] as $key => $v){
			if($v['fromType']==1){
				$page['Rows'][$key]['loginName'] = $v['loginName2'];
			}
			$page['Rows'][$key]['imgSize'] = round($v['imgSize']/1024/1024,2);
			unset($page['Rows'][$key]['loginName2']);
		}
		return $page;
	}
	/**
	 * 删除图片
	 */
	public function del(){
		$id = (int)input('id');
		$image = $this->where('imgId',$id)->find();
		$rs = $this->where('imgId',$id)->update(['dataFlag'=>-1]);
		if(false !== $rs){
			$m = WSTConf('CONF.wstMobileImgSuffix');
			$timgPath =  str_replace('.','_thumb.',$image['imgPath']);
			$mimgPath =  str_replace('.',$m.'.',$image['imgPath']);
	        $mtimgPath = str_replace('.',$m.'_thumb.',$image['imgPath']);
	        
			if(file_exists(WSTRootPath()."/".$image['imgPath']))unlink(WSTRootPath()."/".$image['imgPath']); 
			if(file_exists(WSTRootPath()."/".$timgPath))unlink(WSTRootPath()."/".$timgPath); 
			if(file_exists(WSTRootPath()."/".$mimgPath))unlink(WSTRootPath()."/".$mimgPath); 
			if(file_exists(WSTRootPath()."/".$mtimgPath))unlink(WSTRootPath()."/".$mtimgPath); 
			return WSTReturn("删除成功", 1);
		}
		return WSTReturn("删除失败");
	}
}
