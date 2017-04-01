<?php
namespace wstmart\admin\model;
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
 * 文章业务处理
 */
class Articles extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		$key = input('get.key');
		$where = [];
		$where['a.dataFlag'] = 1;
		if($key!='')$where['a.articleTitle'] = ['like','%'.$key.'%'];
		$page = Db::name('articles')->alias('a')
		->join('__ARTICLE_CATS__ ac','a.catId= ac.catId','left')
		->join('__STAFFS__ s','a.staffId= s.staffId','left')
		->where($where)
		->field('a.articleId,a.catId,a.articleTitle,a.isShow,a.articleContent,a.articleKey,a.createTime,ac.catName,s.staffName')
		->order('a.articleId', 'desc')
		->paginate(input('post.pagesize/d'))->toArray();
		if(count($page['Rows'])>0){
			foreach ($page['Rows'] as $key => $v){
				$page['Rows'][$key]['articleContent'] = strip_tags(htmlspecialchars_decode($v['articleContent']));
			}
		}
		return $page;
	}
	
	/**
	 * 显示是否显示/隐藏
	 */
	public function editiIsShow(){
		$id = input('post.id/d');
		$isShow = input('post.isShow/d')?0:1;
		$result = $this->where(['articleId'=>$id])->update(['isShow' => $isShow]);
		if(false !== $result){
			return WSTReturn("操作成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 * 获取指定对象
	 */
	public function getById($id){
		$single = $this->where(['articleId'=>$id,'dataFlag'=>1])->find();
		$singlec = Db::name('article_cats')->where(['catId'=>$single['catId'],'dataFlag'=>1])->field('catName')->find();
		$single['catName']=$singlec['catName'];
		$single['articleContent'] = htmlspecialchars_decode($single['articleContent']);
		return $single;
	}
	
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		WSTUnset($data,'articleId,dataFlag');
		$data["staffId"] = (int)session('WST_STAFF.staffId');
		$data['createTime'] = date('Y-m-d H:i:s');
		$result = $this->validate('Articles.add')->allowField(true)->save($data);
		if(false !== $result){
			return WSTReturn("新增成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 * 编辑
	 */
	public function edit(){
		$articleId = input('post.id/d');
		$data = input('post.');
		WSTUnset($data,'articleId,dataFlag,createTime');
		$data["staffId"] = (int)session('WST_STAFF.staffId');
		$result = $this->validate('Articles.edit')->allowField(true)->save($data,['articleId'=>$articleId]);
		if(false !== $result){
			return WSTReturn("修改成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 * 删除
	 */
	public function del(){
		$id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
		$result = $this->where(['articleId'=>$id])->update($data);
		if(false !== $result){
			return WSTReturn("删除成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}

	/**
	 * 批量删除
	 */
	public function delByBatch(){
		$ids = input('post.ids');
		$data = [];
		$data['dataFlag'] = -1;
		$result = $this->where(['articleId'=>['in',$ids]])->update($data);
		if(false !== $result){
			return WSTReturn("删除成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
}