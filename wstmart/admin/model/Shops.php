<?php
namespace wstmart\admin\model;
use think\Db;
use think\Loader;
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
 * 店铺业务处理
 */
class Shops extends Base{
	/**
	 * 分页
	 */
	public function pageQuery($shopStatus=1){
		return Db::table('__SHOPS__')->alias('s')->join('__AREAS__ a2','s.areaId=a2.areaId','left')
		       ->where(['s.dataFlag'=>1,'s.shopStatus'=>$shopStatus])
		       ->field('shopId,shopSn,shopName,a2.areaName,shopkeeper,telephone,shopAddress,shopCompany,shopAtive,shopStatus')
		       ->order('shopId desc')->paginate(input('pagesize/d'));
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
	    $result = $this->update($data,['shopId'=>$id]);
	    WSTUnuseImage('shops','shopImg',$id);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
	/**
	 * 获取店铺信息
	 */
	public function getById($id){
		$shop = $this->get(['dataFlag'=>1,'shopId'=>$id])->toArray();
		//获取经营范围
		$goodscats = Db::name('cat_shops')->where('shopId',$id)->select();
		$shop['catshops'] = [];
		foreach ($goodscats as $v){
			$shop['catshops'][$v['catId']] = true;
		}
		//获取认证类型
	    $shopAccreds = Db::name('shop_accreds')->where('shopId',$id)->select();
	    $shop['accreds'] = [];
		foreach ($shopAccreds as $v){
			$shop['accreds'][$v['accredId']] = true;
		}
		return $shop;
	}
	/**
	 * 生成店铺编号
	 * @param $key 编号前缀,要控制不要超过int总长度，最好是一两个字母
	 */
	public function getShopSn($key = ''){
		$rs = $this->Max("REPLACE(shopSn,'S','')+''");
		if($rs==''){
			return $key.'000000001';
		}else{
			for($i=0;$i<1000;$i++){
			   $num = (int)str_replace($key,'',$rs);
			   $shopSn = $key.sprintf("%09d",($num+1));
			   $ischeck = $this->checkShopSn($shopSn);
			   if(!$ischeck)return $shopSn;
			}
			return '';//一直都检测到那就不要强行添加了
		}
	}
	
	/**
	 * 检测店铺编号是否存在
	 */
	public function checkShopSn($shopSn,$shopId=0){
		$dbo = $this->where(['shopSn'=>$shopSn,'dataFlag'=>1]);
		if($shopId>0)$dbo->where('shopId','<>',$shopId);
		$num = $dbo->Count();
		if($num==0)return false;
		return true;
	}
	

	/**
	 * 新增
	 */
	public function add(){
		//判断是不是从开店申请里过来的，还要检测申请人身份[会员/游客]
		$applyId = input('post.applyId/d');
		$userId = 0;
		if($applyId>0){
			$applys = model('ShopApplys')->checkOpenShop($applyId);
			$userId = (int)$applys['userId'];
		}
		//如果是游客的话就要检测一下账号是否存在
		if($userId==0){
			$user = [];
			$user['loginName'] = Input('post.loginName');
			$user['loginPwd'] = Input('post.loginPwd');
			$ck = WSTCheckLoginKey($user['loginName']);
			if($ck['status']!=1)return $ck;
			if($user['loginPwd']=='')$user['loginPwd'] = '88888888';
			$user["loginSecret"] = rand(1000,9999);
	    	$user['loginPwd'] = md5($user['loginPwd'].$user['loginSecret']);
	    	$user["userType"] = 1;
	    	$user['createTime'] = date('Y-m-d H:i:s');
		}
    	$validate = Loader::validate('Shops');
        if(!$validate->check(Input('post.')))return WSTReturn($validate->getError());
        //判断经营范围
        $goodsCatIds = Input('post.goodsCatIds');
        $accredIds = Input('post.accredIds');
        if($goodsCatIds=='')return WSTReturn('请选择经营范围');
        Db::startTrans();
        try{
        	//如果是游客的话就先新增会员资料
        	if($userId==0){
	            model('users')->save($user);
	            $userId = model('users')->userId;
        	}else{
        		model('users')->where('userId',$userId)->update(['userType'=>1]);
        	}
	        $data = Input('post.');
	        $data['createTime'] = date('Y-m-d H:i:s');
	        //获取地区
	        $areaIds = model('Areas')->getParentIs($data['areaId']);
		    if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
		    $areaIds = model('Areas')->getParentIs($data['bankAreaId']);
		    if(!empty($areaIds))$data['bankAreaIdPath'] = implode('_',$areaIds)."_";
	        WSTUnset($data,'shopId,dataFlag,isSelf');
	        if($data['shopSn']=='')$data['shopSn'] = $this->getShopSn('S');
	        $data['userId'] = $userId;
	        $shopId = 0;
	        if($userId>0){
	        	$this->allowField(true)->save($data);
	        	$shopId = $this->shopId;
	        	//启用上传图片
				WSTUseImages(1, $shopId, $data['shopImg']);
	        	//建立店铺配置信息
	        	$sc = [];
	        	$sc['shopId'] = $shopId;
	        	Db::name('ShopConfigs')->insert($sc);
	        	//建立店铺评分记录
				$ss = [];
				$ss['shopId'] = $shopId;
				Db::name('shop_scores')->insert($ss);
	        	if(Input('post.applyId/d')>0)model('ShopApplys')->editApplyOpenStatus(Input('post.applyId/d'),$shopId);
		        //经营范围
		        $goodsCats = explode(',',$goodsCatIds);
		        foreach ($goodsCats as $v){
		        	if((int)$v>0)Db::name('cat_shops')->insert(['shopId'=>$shopId,'catId'=>$v]);
		        }
		        //认证类型
	            if($accredIds!=''){
	                $accreds = explode(',',$accredIds);
		            foreach ($accreds as $v){
			        	if((int)$v>0)Db::name('shop_accreds')->insert(['shopId'=>$shopId,'accredId'=>$v]);
			        }
	            }
	        }
	        Db::commit();
	        return WSTReturn("新增成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败',-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$shopId = input('post.shopId/d',0);
		$validate = Loader::validate('Shops');
        if(!$validate->check(Input('post.')))return WSTReturn($validate->getError());
        //判断经营范围
        $goodsCatIds = input('post.goodsCatIds');
        $accredIds = input('post.accredIds');
        if($goodsCatIds=='')return WSTReturn('请选择经营范围');
        Db::startTrans();
        try{
	        $data = input('post.');
	        //获取地区
	        $areaIds = model('Areas')->getParentIs($data['areaId']);
		    if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
		    $areaIds = model('Areas')->getParentIs($data['bankAreaId']);
		    if(!empty($areaIds))$data['bankAreaIdPath'] = implode('_',$areaIds)."_";
	        WSTUnset($data,'shopId,userId,dataFlag,createTime,applyId,goodsCatIds,accredIds,isSelf');
	        //启用上传图片
			WSTUseImages(1, $shopId, $data['shopImg'],'shops','shopImg');
	        $this->allowField(true)->save($data,['shopId'=>$shopId,'dataFlag'=>1]);
		    //经营范围
		    Db::name('cat_shops')->where('shopId','=',$shopId)->delete();
		    $goodsCats = explode(',',$goodsCatIds);
		    foreach ($goodsCats as $key =>$v){
		        if((int)$v>0){
		        	Db::name('cat_shops')->insert(['shopId'=>$shopId,'catId'=>$v]);
		        }
		    }
		    //认证类型
		    Db::name('shop_accreds')->where('shopId','=',$shopId)->delete();
	        if($accredIds!=''){
	            $accreds = explode(',',$accredIds);
		        foreach ($accreds as $key =>$v){
			        if((int)$v>0){
			        	Db::name('shop_accreds')->insert(['shopId'=>$shopId,'accredId'=>$v]);
			        }
			    }
	        }
	        Db::commit();
	        return WSTReturn("编辑成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            print_r($e);
            return WSTReturn('编辑失败',-1);
        }
	}
	/**
	* 获取所有店铺id
	*/
	public function getAllShopId(){
		return $this->where(['dataFlag'=>1,'shopStatus'=>1])->column('shopId');
	}
	
	/**
	 * 搜索经验范围的店铺
	 */
	public function searchQuery(){
		$goodsCatatId = (int)input('post.goodsCatId');
		if($goodsCatatId<=0)return [];
		$key = input('post.key');
		$where = [];
		$where['dataFlag'] = 1;
		$where['shopStatus'] = 1;
		$where['catId'] = $goodsCatatId;
		if($key!='')$where['shopsName|shopSn'] = ['like','%'.$key.'%'];
		return $this->alias('s')->join('__CAT_SHOPS__ cs','s.shopId=cs.shopId','inner')
		            ->where($where)->field('shopName,s.shopId,shopSn')->select();
	}
	
    /**
	 * 自营自动登录
	 */
	public function selfLogin($id){
		$shopId = $id;
		$userid = $this->where(["dataFlag"=>1, "shopStatus"=>1,"shopId"=>$shopId])->field('userId')->find();
		if(!empty($userid['userId'])){
			$userId = $userid['userId'];
			//获取用户信息
			$u = new Users();
			$rs = $u->getById($userId);
			//获取用户等级
			$rrs = Db::name('user_ranks')->where('startScore','<=',$rs['userTotalScore'])->where('endScore','>=',$rs['userTotalScore'])->field('rankId,rankName,rebate,userrankImg')->find();
			$rs['rankId'] = $rrs['rankId'];
			$rs['rankName'] = $rrs['rankName'];
			$rs['userrankImg'] = $rrs['userrankImg'];
			$ip = request()->ip();
			$u->where(["userId"=>$userId])->update(["lastTime"=>date('Y-m-d H:i:s'),"lastIP"=>$ip]);
			//加载店铺信息
			$shops= new Shops();
			$shop = $shops->where(["userId"=>$userId,"dataFlag" =>1])->find();
			if(!empty($shop))$rs = array_merge($shop->toArray(),$rs->toArray());
			//记录登录日志
			$data = array();
			$data["userId"] = $userId;
			$data["loginTime"] = date('Y-m-d H:i:s');
			$data["loginIp"] = $ip;
			Db::name('log_user_logins')->insert($data);
			session('WST_USER',$rs);
			return WSTReturn("","1");
		}
		return WSTReturn("",-1);
	}
	
}
