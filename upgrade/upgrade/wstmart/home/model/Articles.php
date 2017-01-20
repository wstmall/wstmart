<?php
namespace wstmart\home\model;
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
 * 文章类
 */
use think\Db;
class Articles extends Base{
	/**
	 * 获取帮助左侧列表
	 */
	public function helpList(){
		$arts = cache('arts');
		if(!$arts){
			$rs = $this->alias('a')->join('article_cats ac','a.catId=ac.catId','inner')
				  ->field('a.articleId,a.catId,a.articleTitle,ac.catName')
				  ->where(['a.dataFlag'=>1,
				  		   'ac.dataFlag'=>1,
				  		   'ac.isShow'=>1,
				  		   'ac.parentId'=>7])
				  ->cache(true)
				  ->select();
			//同一分类下的文章放一起
			$catName = [];
			$arts = [];
			foreach($rs as $k=>$v){
				if(in_array($v['catName'],$catName)){
					$arts[$v['catName'].'-'.$v['catId']][] = $v;
				}else{
					$catName[] = $v['catName'];
					$arts[$v['catName'].'-'.$v['catId']][] = $v;
				}
			}
			cache('arts',$arts,86400);
		}
		return $arts;
	}
	/**
	*  根据id获取帮助文章
	*/
	public function getHelpById(){
		$id = (int)input('id');
		return $this->alias('a')->join('__ARTICLE_CATS__ ac','a.catId=ac.catId','inner')->where('ac.parentId=7 and a.dataFlag=1')->cache(true)->find($id);
	}
	/**
	*  根据id获取资讯文章
	*/
	public function getNewsById(){
		$id = (int)input('id');
		return $this->alias('a')->join('__ARTICLE_CATS__ ac','a.catId=ac.catId','inner')->where('a.catId<>7 and ac.parentId<>7 and a.dataFlag=1')->cache(true)->find($id);
	}

	/**
	* 获取资讯列表
	*/
	public function NewsList(){
		$list =  $this->getTree();
		foreach($list as $k=>$v){
			if(!empty($v['children'])){
				foreach($v['children'] as $k1=>$v1){
					// 二级分类下的文章总条数
					$list[$k]['children'][$k1]['newsCount'] = $this->where(['catId'=>$v1['catId'],
																	'dataFlag'=>1])->cache(true)->count();
				}
			}
		}
		return $list;
	}

	public function getTree(){
		$artTree = cache('artTree');
		if(!$artTree){
			$data = Db::name('article_cats')->field('catName,catId,parentId')->where('parentId <> 7 and catId <> 7 and dataFlag=1')->cache(true)->select();
			$artTree = $this->_getTree($data, 0);
			cache('artTree',$artTree,86400);
		}
		return $artTree;
	}
	public function _getTree($data,$parentId){
		$tree = [];
		foreach($data as $k=>$v){
			if($v['parentId']==$parentId){
				// 再找其下级分类
				$v['children'] = $this->_getTree($data,$v['catId']);
				$tree[] = $v;
			}
		}
		return $tree;
	}
	/**
	*	根据分类id获取文章列表
	*/
	public function nList(){
		$catId = (int)input('catId');
		$rs = $this->alias('a')
			  ->join('__ARTICLE_CATS__ ac','a.catId=ac.catId','inner')
			  ->field('a.*')
			  ->where(['a.catId'=>$catId,
			  		   'ac.dataFlag'=>1,
			  		   'ac.isShow'=>1,
			  		   'ac.parentId'=>['<>',7],
			  		   ])
			  ->cache(true)
			  ->paginate();
		return $rs;
	}
	/**
	* 面包屑导航
	*/
	public function bcNav(){
		$catId = (int)input('catId'); //分类id
		$artId = (int)input('id'); 	//文章id
		$data = Db::name('article_cats')->field('catId,parentId,catName')->cache(true)->select();
		if($artId){
			$catId = $this->where('articleId',$artId)->value('catId');
		}
		$bcNav = $this->getParent($data,$catId,$isClear=true);
		return $bcNav;

	}
	/**
	* 获取父级分类
	*/
	public function getParent($data, $catId,$isClear=false){
		static $bcNav = [];
		if($isClear)
			$bcNav = [];
		foreach($data as $k=>$v){
			if($catId == $v['catId']){
				if($catId!=0){
					$this->getParent($data, $v['parentId']);
					$bcNav[] = $v;
				}
			}
		}
		return $bcNav;
	}

	/**
	*  记录解决情况
	*/
	public function recordSolve(){
		$articleId =  (int)input('id');
		$status =  (int)input('status');
		if($status==1){
			$rs = $this->where('articleId',$articleId)->setInc('solve');
		}else{
			$rs = $this->where('articleId',$articleId)->setInc('unsolve');
		}
		if($rs!==false){
			return WSTReturn('操作成功',1);
		}else{
			return WSTReturn('操作失败',-1);
		}
	}

	/**
	* 获取资讯中心的子集分类id
	*/
	public function getChildIds(){
		$ids = [];
		$data = Db::name('article_cats')->cache(true)->select();
			foreach($data as $k=>$v){
				if($v['parentId']!=7 && $v['catId']!=7 && $v['parentId']!=0 ){
					$ids[] = $v['catId'];
				}
			}
		return $ids;
	}

	/**
	* 获取咨询中中心所有文章
	*/
	public function getArticles(){
		// 获取咨询中心下的所有分类id
		$ids = $this->getChildIds();
		$rs = $this->alias('a')
			  ->field('a.*')
			  ->join('__ARTICLE_CATS__ ac','a.catId=ac.catId','inner')
			  ->where(['a.catId'=>['in',$ids],
			  		   'ac.dataFlag'=>1,
			  		   'ac.isShow'=>1,
			  		   'ac.parentId'=>['<>',7],
			  		   ])
			  ->distinct(true)
			  ->cache(true)
			  ->paginate(15);
		return $rs;
	}
}
