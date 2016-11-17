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
 * 文章分类业务处理
 */
use think\Db;
class ArticleCats extends Base{
	/**
	 * 获取树形分类
	 */
	public function pageQuery(){
		$parentId = input('catId/d',0);
		$data = $this->where(['dataFlag'=>1,'parentId'=>$parentId])->order('catId desc')->paginate(input('post.pagesize/d'))->toArray();
		return $data;
	}
	/**
	 * 获取列表
	 */
	public function listQuery($parentId){
		$rs = $this->where(['dataFlag'=>1,'parentId'=>$parentId])->order('catSort asc,catName asc')->select();
		if(count($rs)>0){
			foreach ($rs as $key => $v){
				$rs[$key]['childrenurl'] = url('admin/articlecats/listQuery',array('parentId'=>$v['catId']));
				$rs[$key]['children'] = [];
				$rs[$key]['isextend'] = false;
			}
		}
		return $rs;
	}
	/**
	 * 获取指定对象
	 */
	public function getById($id){
		return $this->get(['dataFlag'=>1,'catId'=>$id]);
	}
	
	/**
	 *  获取文章分类列表
	 */
	public function listQuery2(){
		return $this->where(['dataFlag'=>1,'isShow'=>1])->field('catId,catName,parentId')->order('catSort desc')->select();
	}
	
	/**
	 * 显示是否显示/隐藏
	 */
	public function editiIsShow(){
		$ids = array();
		$id = input('post.id/d');
		$ids = $this->getChild($id);
		$isShow = input('post.isShow/d')?1:0;
		$result = $this->where("catId in(".implode(',',$ids).")")->update(['isShow' => $isShow]);
		if(false !== $result){
			return WSTReturn("操作成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 * 迭代获取下级
	 * 获取一个分类下的所有子级分类id
	 */
	public function getChild($pid=1){
		$data = $this->where("dataFlag=1")->select();
		//获取该分类id下的所有子级分类id
		$ids = $this->_getChild($data, $pid, true);//每次调用都清空一次数组
		//把自己也放进来
		array_unshift($ids, $pid);
		return $ids;
	}
	public function _getChild($data, $pid, $isClear=false){
		static $ids = array();
		if($isClear)//是否清空数组
			$ids = array();
		foreach($data as $k=>$v)
		{
			if($v['parentId']==$pid && $v['dataFlag']==1)
			{
				$ids[] = $v['catId'];//将找到的下级分类id放入静态数组
				//再找下当前id是否还存在下级id
				$this->_getChild($data, $v['catId']);
			}
		}
		return $ids;
	}
	
	/**
	 * 新增
	 */
	public function add(){
		$parentId = input('post.parentId/d');
		$data = input('post.');
		WSTUnset($data,'catId,catType,dataFlag');
		$data['parentId'] = $parentId;
		$data['createTime'] = date('Y-m-d H:i:s');
		$result = $this->validate('ArticleCats.add')->allowField(true)->save($data);
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
		$catId = input('post.id/d');
		$result = $this->validate('ArticleCats.edit')->allowField(['catName','isShow','catSort'])->save(input('post.'),['catId'=>$catId]);
		$ids = array();
		$ids = $this->getChild($catId);
		$this->where("catId in(".implode(',',$ids).")")->update(['isShow' => input('post.')['isShow']]);
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
		$ids = array();
		$id = input('post.id/d');
		$ids = $this->getChild($id);
		$data = [];
		$data['dataFlag'] = -1;
		$rs = $this->getById($id);
		if($rs['catType']==1){
			return WSTReturn("不能删除该分类", -1);
		}else{
			Db::startTrans();
            try{
				$result = $this->where("catId in(".implode(',',$ids).")")->update($data);
				if(false !==$result){
					Db::name('articles')->where(['catId'=>['in',$ids]])->update(['dataFlag'=>-1]);
				}
				Db::commit();
	            return WSTReturn("删除成功", 1);
            }catch (\Exception $e) {
                Db::rollback();
                return WSTReturn('删除失败',-1);
            }
		}
	}
}