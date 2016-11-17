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
 * 门店分类
 */
class ShopCats extends Base{
	
	/**
	 * 批量保存商品分类
	 */
	public function batchSaveCats(){
		
		$shopId = (int)session('WST_USER.shopId');
		$createTime = date("Y-m-d H:i:s");
		//先保存了已经有父级的分类
		$otherNo = input('post.otherNo/d');
		for($i=0;$i<$otherNo;$i++){
			
			$data = array();
			$data['catName'] = input('post.catName_o_'.$i);
			if($data['catName']=='')continue;
			$data['shopId'] = $shopId;
			$data['parentId'] = input('post.catId_o_'.$i."/d");
			$data['catSort'] = input('post.catSort_o_'.$i."/d");
			$data['isShow'] = input('post.catShow_o_'.$i."/d");
			$data['createTime'] = $createTime;
			$rs = $this->where(["dataFlag"=>1,"shopId"=>$shopId,"catId"=>$data['parentId']])->find();
			if(empty($rs))continue;
			$this->isUpdate(false)->allowField(["catName","shopId","parentId","catSort","isShow"])->save($data);
		}
		
		//保存没有父级分类的
		$fristNo = input('post.fristNo/d');
	    for($i=0;$i<$fristNo;$i++){
			$data = array();
			
			$data['catName'] = input('post.catName_'.$i);
			if($data['catName']=='')continue;
			$data['parentId'] = 0;
			$data['shopId'] = $shopId;
			$data['catSort'] = input('post.catSort_'.$i."/d");
			$data['isShow'] = input('post.catShow_'.$i."/d");
			$data['createTime'] = $createTime;
			$parentId = $this->isUpdate(false)->allowField(["catName","shopId","parentId","catSort","isShow"])->save($data);
			$parentId = $this->catId;
			if(false !== $parentId){
				//新增子类
				$catSecondNo = (int)input('post.catSecondNo_'.$i."/d");
		        for($j=0;$j<$catSecondNo;$j++){
					$data = array();
					$data['catName'] = input('post.catName_'.$i."_".$j);
					if($data['catName']=='')continue;
					$data['shopId'] = $shopId;
					$data['parentId'] = $parentId;
					$data['catSort'] = input('post.catSort_'.$i."_".$j."/d");
					$data['isShow'] = input('post.catShow_'.$i."_".$j."/d");
					$data['createTime'] = $createTime;
					$this->isUpdate(false)->allowField(["catName","shopId","parentId","catSort","isShow"])->save($data);
			    }
			}
		}
		return WSTReturn("",1);
	}
	
	 /**
	  * 修改名称
	  */
	 public function editName(){
	 	$rd = array('status'=>-1);
	 	$id = input("post.id/d");
		$data = array();
		$data["catName"] = input("catName");
		$shopId = (int)session('WST_USER.shopId');
		
		$rs = $this->validate("ShopCats.edit")->save($data,["catId"=>$id,"shopId"=>$shopId]);
		if(false !== $rs){
			return WSTReturn("",1);
		}
		return WSTReturn($this->getError());
	 } 
	 /**
	  * 修改排序号
	  */
	 public function editSort(){
	 	$rd = array('status'=>-1);
	 	$id = input("post.id/d");
		$data = array();
		$data["catSort"] = input("post.catSort/d");
		$shopId = (int)session('WST_USER.shopId');
		$rs = $this->save($data,["catId"=>$id,"shopId"=>$shopId]);
		if(false !== $rs){
			return WSTReturn("",1);
		}
		return WSTReturn($this->getError());
	 } 
	 /**
	  * 获取指定对象
	  */
     public function getById($id){
		return $this->where(["catId"=>(int)$id])->find();
	 }
	 
	 /**
	  * 获取树形分类
	  */
	public function getCatAndChild($shopId){
	 	 //获取第一级分类
	 	 $rs1 = $this->where(['shopId'=>$shopId,'dataFlag'=>1,'parentId'=>0])->order('catSort asc')->select();
	 	 if(count($rs1)>0){
	 	 	$ids = array();
	 	 	foreach ($rs1 as $key => $v){
	 	 		$ids[] = $v['catId'];
	 	 	}
	 	 	$rs2 = $this->where(['shopId'=>$shopId,'dataFlag'=>1])
	 	 				->where('parentId', 'in', implode(',',$ids))
	 	 				->order('catSort asc,catId asc')->select();
	 	 	if(count($rs2)>0){
	 	 		$tmpArr = array();
	 	 		foreach ($rs2 as $key => $v){
	 	 			$tmpArr[$v['parentId']][] = $v;
	 	 		}
	 	 		foreach ($rs1 as $key => $v){
	 	 			$rs1[$key]['child'] = array_key_exists($v['catId'],$tmpArr)?$tmpArr[$v['catId']]:null;
	 	 			$rs1[$key]['childNum'] = array_key_exists($v['catId'],$tmpArr)?count($tmpArr[$v['catId']]):0;;
	 	 		}
	 	 	}
		}
		return $rs1;
	}
	 
	/**
	* 获取列表
	*/
	public function listQuery($shopId,$parentId){
		$rs = $this->where(['shopId'=>$shopId,'dataFlag'=>1,'isShow'=>1,'parentId'=>$parentId,'shopId'=>$shopId])
				   ->order('catSort asc')->select();
		return $rs;
	}
	  
	 /**
	  * 删除
	  */
	 public function del(){
	 	$id = input("post.id/d");
	 	if($id==0)return $rd;
		$shopId = (int)session('WST_USER.shopId');
		//把相关的商品下架了
		$data = array();
		$data['isSale'] = 0;
		$gm = new \wstmart\home\model\Goods();
		$gm->save($data,['shopId'=>$shopId,"shopCatId1"=>$id]);
		$gm->save($data,['shopId'=>$shopId,"shopCatId2"=>$id]);
		//删除商品分类
		$data = array();
		$data["dataFlag"] = -1;
	 	$rs = $this->where("catId|parentId",$id)->where(["shopId"=>$shopId])->update($data);
	    if(false !== $rs){
			return WSTReturn("",1);
		}else{
			return WSTReturn($this->getError());
		}
		
	 }
	 
	 
	/**
	  * 获取店铺商品分类列表
	*/
    public function getShopCats($shopId = 0){
		$data = [];
		if(!$data){
			$data = $this->field("catId,parentId,catName,shopId")->where(["shopId"=>$shopId,"parentId"=>0,"isShow"=>1 ,"dataFlag"=>1])->order("catSort asc")->select();
			if(count($data)>0){
				$ids = array();
				foreach ($data as $v){
					$ids[] = $v['catId'];
				}
				
				$crs = $this->field("catId,parentId,catName,shopId")
							->where(["shopId"=>$shopId,"isShow"=>1 ,"dataFlag"=>1])
							->where("parentId","in",implode(',',$ids))
							->order("catSort asc")->select();
				$ids = array();
			    foreach ($crs as $v){
			    	$ids[$v['parentId']][] = $v;
				}
				foreach ($data as $key =>$v){
					$data[$key]['children'] = '';
					if(isset($ids[$v['catId']])){
						$data[$key]['children'] = $ids[$v['catId']];
					}
				}
			}
	    }
		return $data;
	}
	
	/**
	 * 显示状态
	 */
	public function changeCatStatus(){
		$id = input("post.id/d");
		$isShow = input("post.isShow/d");
		$parentId = input("post.pid/d");
		$data = array();
		$data["isShow"] = $isShow;
		$shopId = (int)session('WST_USER.shopId');

		$this->save($data,["catId"=>$id,"shopId"=>$shopId]);
		$this->save($data,["parentId"=>$id,"shopId"=>$shopId]);
		if($parentId>0 && $isShow==1){
			$this->save($data,["catId"=>$parentId,"shopId"=>$shopId]);
		}
		//如果是隐藏的话还要下架的商品
		if($isShow==0){
			$gm = new \wstmart\home\model\Goods();
			$data = array();
			$data["isSale"] = 0;
			$gm->save($data,["shopId"=>$shopId,"shopCatId1|shopCatId2"=>['=',$id]]);
		}
		return WSTReturn("",1);
	}
	
	 /**
     * 获取自营店铺首页楼层
     */
    public function getFloors(){
    	$shopId = (int)input('shopId');
	    $cats1 = $this->where(['dataFlag'=>1, 'isShow' => 1,'parentId'=>0,'shopId'=>$shopId])
		             ->field("catName,catId")->order('catSort asc')->select();
		if(!empty($cats1)){
			$ids = [];
			foreach ($cats1 as $key =>$v){
				$ids[] = $v['catId'];
			}
			$cats2 = [];
			$rs = $this->where(['dataFlag'=>1, 'isShow' => 1,'parentId'=>['in',$ids],'shopId'=>$shopId])
				             ->field("parentId,catName,catId")->order('catSort asc')->select();
			foreach ($rs as $key => $v){
				$cats2[$v['parentId']][] = $v;
			}
			foreach ($cats1 as $key =>$v){
				$cats1[$key]['children'] = (isset($cats2[$v['catId']]))?$cats2[$v['catId']]:[];
			}
		}
		return $cats1;
    }
}
