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
 * 商品分类业务处理
 */
use think\Db;
class GoodsCats extends Base{
	/**
	 * 获取树形分类
	 */
	public function pageQuery(){
		return $this->where(['dataFlag'=>1,'parentId'=>input('catId/d',0)])->order('catSort asc,catId desc')->paginate(input('post.pagesize/d'))->toArray();
	}
	/**
	 * 获取列表
	 */
	public function listQuery($parentId){
		return $this->where(['dataFlag'=>1,'parentId'=>$parentId])->order('catSort asc,catName asc')->select();
	}
	
	/**
	 *获取商品分类名值对
	 */
	public function listKeyAll(){
		$rs = $this->field("catId,catName")->where(['dataFlag'=>1])->order('catSort asc,catName asc')->select();
		$data = array();
		foreach ($rs as $key => $cat) {
			$data[$cat["catId"]] = $cat["catName"];
		}
		return $data;
	}
	
	/**
	 *	获取树形分类
	 */
	public function getTree($data, $parentId=0){
		$arr = array();
		foreach($data as $k=>$v)
		{
			if($v['parentId']==$parentId && $v['dataFlag']==1)
			{
				//再查找该分类下是否还有子分类
				$v['child'] = $this->getTree($data, $v['catId']);
				//统计child
				$v['childNum'] = count($v['child']);
				//将找到的分类放回该数组中
				$arr[]=$v;
			}
		}
		return $arr;
	}
	
	/**
	 * 迭代获取下级
	 * 获取一个分类下的所有子级分类id
	 */
	public function getChild($pid){
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
	 * 获取指定对象
	 */
	public function getGoodscats($id){
		return $this->where(['catId'=>$id])->find();
	}
	 
	 /**
	  * 显示是否推荐/不推荐
	  */
	 public function editiIsFloor(){
	    $ids = array();
		$id = input('post.id/d');
		$ids = $this->getChild($id);
	 	$isFloor = input('post.isFloor/d')?1:0;
	 	$result = $this->where("catId in(".implode(',',$ids).")")->update(['isFloor' => $isFloor]);
	 	if(false !== $result){
	 		return WSTReturn("操作成功", 1);
	 	}else{
	 		return WSTReturn($this->getError(),-1);
	 	}
	 }
	
	/**
	 * 显示是否显示/隐藏
	 */
	public function editiIsShow(){
		$ids = array();
		$id = input('post.id/d');
		$ids = $this->getChild($id);
		$isShow = input('post.isShow/d')?1:0;
		Db::startTrans();
        try{
			$result = $this->where("catId in(".implode(',',$ids).")")->update(['isShow' => $isShow]);
			if(false !== $result){
				if($isShow==0){
					//删除购物车里的相关商品
					$goods = Db::name('goods')->where(["goodsCatId"=>['in',$ids],'isSale'=>1])->field('goodsId')->select();
					if(count($goods)>0){
						$goodsIds = [];
						foreach ($goods as $key =>$v){
							$goodsIds[] = $v['goodsId'];
						}
						Db::name('carts')->where(['goodsId'=>['in',$goodsIds]])->delete();
					}
					//把相关的商品下架了
					Db::name('goods')->where("goodsCatId in(".implode(',',$ids).")")->update(['isSale' => 0]);
				}
		    }
		    Db::commit();
	        return WSTReturn("操作成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('删除失败',-1);
        }
			
	}
	
	/**
	 * 新增
	 */
	public function add(){
		$parentId = input('post.parentId/d');
		$data = input('post.');
		WSTUnset($data,'catId,dataFlag');
		$data['parentId'] = $parentId;
		$data['createTime'] = date('Y-m-d H:i:s');
		$result = $this->validate('GoodsCats.add')->allowField(true)->save($data);
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
		$data = input('post.');
		WSTUnset($data,'catId,dataFlag,createTime');
		$result = $this->validate('GoodsCats.edit')->allowField(true)->save($data,['catId'=>$catId]);
		$ids = array();
		$ids = $this->getChild($catId);
		$this->where("catId in(".implode(',',$ids).")")->update(['isShow' => (int)$data['isShow'],'isFloor'=> $data['isFloor'],'commissionRate'=>(float)$data['commissionRate']]);
		if(false !== $result){
			if($data['isShow']==0){
				//删除购物车里的相关商品
				$goods = Db::name('goods')->where(["goodsCatId"=>['in',$ids],'isSale'=>1])->field('goodsId')->select();
				if(count($goods)>0){
					$goodsIds = [];
					foreach ($goods as $key =>$v){
							$goodsIds[] = $v['goodsId'];
					}
					Db::name('carts')->where(['goodsId'=>['in',$goodsIds]])->delete();
				}
		    	//把相关的商品下架了
		        Db::name('goods')->where("goodsCatId in(".implode(',',$ids).")")->update(['isSale' => 0]);
			}
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
		Db::startTrans();
        try{
		    $data = [];
		    $data['dataFlag'] = -1;
		    $result = $this->where("catId in(".implode(',',$ids).")")->update($data);
		    if(false !== $result){
		        //删除购物车里的相关商品
				$goods = Db::name('goods')->where(["goodsCatId"=>['in',$ids],'isSale'=>1])->field('goodsId')->select();
				if(count($goods)>0){
					$goodsIds = [];
					foreach ($goods as $key =>$v){
							$goodsIds[] = $v['goodsId'];
					}
					Db::name('carts')->where(['goodsId'=>['in',$goodsIds]])->delete();
				}
		    	//把相关的商品下架了
		        Db::name('goods')->where("goodsCatId in(".implode(',',$ids).")")->update(['isSale' => 0]);
		    }
            Db::commit();
	        return WSTReturn("删除成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('删除失败',-1);
        }
	}
	
    /**
	 * 根据子分类获取其父级分类
	 */
	public function getParentIs($id,$data = array()){
		$data[] = $id;
		$parentId = $this->where('catId',$id)->value('parentId');
		if($parentId==0){
			krsort($data);
			return $data;
		}else{
			return $this->getParentIs($parentId, $data);
		}
	}
}