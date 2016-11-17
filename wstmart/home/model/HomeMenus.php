<?php
namespace wstmart\home\model;
use think\Db;
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
 * 菜单业务处理
 */
class HomeMenus extends Base{
	/**
	 * 获取菜单树
	 */
	public function getMenus(){
		$data = cache('WST_HOME_MENUS');
		if(!$data){
			$rs = $this->where(['isShow'=>1,'dataFlag'=>1])
			        ->field('menuId,parentId,menuName,menuUrl,menuType')->order('menuSort asc,menuId asc')->select();
			$m1 = ['0'=>[],'1'=>[]];
			$tmp = [];
			
			//获取第一级
			foreach ($rs as $key => $v){
				if($v['parentId']==0){
					$m1[$v['menuType']][$v['menuId']] = ['menuId'=>$v['menuId'],'parentId'=>$v['parentId'],'menuName'=>$v['menuName'],'menuUrl'=>$v['menuUrl']];
				}else{
					$tmp[$v['parentId']][] = ['menuId'=>$v['menuId'],'parentId'=>$v['parentId'],'menuName'=>$v['menuName'],'menuUrl'=>$v['menuUrl']];
				}
			}
			//获取第二级
			foreach ($m1 as $key => $v){
				foreach ($v as $key1 => $v1){
				    if(isset($tmp[$v1['menuId']]))$m1[$key][$key1]['list'] = $tmp[$v1['menuId']];
				}
			}
			//获取第三级
		    foreach ($m1 as $key => $v){
		    	foreach ($v as $key1 => $v1){
			    	if(isset($v1['list'])){
				    	foreach ($v1['list'] as $key2 => $v2){
						    if(isset($tmp[$v2['menuId']]))$m1[$key][$key1]['list'][$key2]['list'] = $tmp[$v2['menuId']];
				    	}
			    	}
		    	}
			}
			cache('WST_HOME_MENUS',$m1,31536000);
			return $m1;
		}
		return $data;
	}
	
	/**
	 * 获取菜单URL
	 */
	public function getMenusUrl(){
		$data = cache('WST_PRO_MENUS');
		if(!$data){
			$list = $this->where('dataFlag',1)->order('menuType asc')->select();
			$menus = [];
			foreach($list as $key => $v){
				$menus[strtolower($v['menuUrl'])] = $v['menuType'];
				if($v['menuOtherUrl']!=''){
					$str = explode(',',$v['menuOtherUrl']);
					foreach ($str as $vkey => $vv){
						if($vv=='')continue;
						$menus[strtolower($vv)] = $v['menuType'];
					}
				}
			}
			cache('WST_PRO_MENUS',$menus,31536000);
			return $menus;
		}
		return $data;
	}
}
