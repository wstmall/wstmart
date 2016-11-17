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
 * 结算类
 */
class Settlements extends Base{
    /**
     * 获取已结算的结算单列表
     */
    public function pageQuery(){
        $where = [];
        if(input('settlementNo')!='')$where['settlementNo'] = ['like','%'.input('settlementNo').'%'];
        if((int)input('isFinish')>=0)$where['settlementStatus'] = (int)input('isFinish');
    	return Db::name('settlements')->alias('s')->where($where)->order('settlementId', 'desc')
			->paginate(input('pagesize/d'));
    }
    /**
     *  获取未结算订单列表
     */
    public function pageUnSettledQuery(){
    	$where = [];
    	if(input('orderNo')!='')$where['orderNo'] = ['like','%'.input('orderNo').'%'];
    	$where['dataFlag'] = 1;
    	$where['orderStatus'] = 2;
    	$where['settlementId'] = 0;
    	$where['shopId'] = (int)session('WST_USER.shopId');
    	$page =  Db::name('orders')->where($where)->order('orderId', 'desc')
		           ->field('orderId,orderNo,createTime,payType,goodsMoney,deliverMoney,totalMoney,commissionFee,realTotalMoney')
                   ->paginate(input('pagesize/d'))->toArray();
        if(count($page['Rows'])){
            foreach ($page['Rows'] as $key => $v) {
                $page['Rows'][$key]['payTypeNames'] = WSTLangPayType($v['payType']);
            }
        }
        return $page;
    }
    /**
     * 结算指定的订单
     */
    public function settlement(){
    	$shopId = (int)session('WST_USER.shopId');
    	$ids = input('ids');
    	$where['dataFlag'] = 1;
    	$where['orderStatus'] = 2;
    	$where['settlementId'] = 0;
    	$where['orderId'] = ['in',$ids];
    	$where['shopId'] = $shopId;
    	$orders = Db::name('orders')->where($where)->field('orderId,payType,realTotalMoney,commissionFee')->select();
    	if(empty($orders))return WSTReturn('没有需要结算的订单，请刷新后再核对!');
    	$settlementMoney = 0;
        $commissionFee = 0;    //平台要收的佣金
        $ids = [];
    	foreach ($orders as $key => $v) {
            $ids[] = $v['orderId'];
    		if($v['payType']==1){
                $settlementMoney += $v['realTotalMoney'];
            }
            $commissionFee += abs($v['commissionFee']);
    	}
    	
    	$shops = Db::name('shops')->alias('s')->join('banks b','b.bankId=s.bankId','inner')->where('s.shopId',$shopId)->field('b.bankName,s.bankAreaId,bankNo,bankUserName')->find();
    	if(empty($shops))WSTReturn('无效的店铺结算账号!');
    	Db::startTrans();
		try{
            $areaNames  = model('areas')->getParentNames($shops['bankAreaId']);
            $data = [];
            $data['settlementType'] = 0;
            $data['shopId'] = $shopId;
            $data['accName'] = $shops['bankName'];
            $data['accNo'] = $shops['bankNo'];
            $data['accUser'] = $shops['bankUserName'];
            $data['areaName'] = implode('',$areaNames);
            $data['settlementMoney'] = $settlementMoney;
            $data['commissionFee'] = $commissionFee;
            $data['backMoney'] = $settlementMoney-$commissionFee;
            $data['settlementStatus'] = 0;
            $data['createTime'] = date('Y-m-d H:i:s');
            $result = $this->save($data);
            if(false !==  $result){
            	 $this->settlementNo = $this->settlementId.(fmod($this->settlementId,7));
            	 $this->save();
                 Db::name('orders')->where(['orderId'=>['in',$ids]])->update(['settlementId'=>$this->settlementId]);
                 //修改商家订单情况
                 $commissionFee = -1*$commissionFee;//平台要收的佣金就等于商家要付的钱
                 $prefix = config('database.prefix');
                 $upSql = 'update '.$prefix.'shops set noSettledOrderNum=0,paymentMoney=paymentMoney+'.$commissionFee.',noSettledOrderFee=0 where shopId='.$shopId;
                 Db::execute($upSql);
            	 Db::commit();
            	 return WSTReturn('提交结算申请成功，请留意结算信息~',1);
            }
		}catch (\Exception $e) {
            print_r($e);
            Db::rollback();
        }
        return WSTReturn('提交结算申请失败',-1);
    }

    /**
     * 获取已结算订单
     */
    public function pageSettledQuery(){
        $where = [];
        if(input('settlementNo')!='')$where['settlementNo'] = ['like','%'.input('settlementNo').'%'];
        if(input('orderNo')!='')$where['orderNo'] = ['like','%'.input('orderNo').'%'];
        if((int)input('isFinish')>=0)$where['settlementStatus'] = (int)input('isFinish');
        $where['dataFlag'] = 1;
        $where['orderStatus'] = 2;
        $where['o.shopId'] = (int)session('WST_USER.shopId');
    	$page = Db::name('orders')->alias('o')->join('__SETTLEMENTS__ s','o.settlementId=s.settlementId')->where($where)->field('orderId,orderNo,payType,goodsMoney,deliverMoney,totalMoney,o.commissionFee,realTotalMoney,s.settlementTime,s.settlementNo')->order('s.settlementTime desc')->paginate(input('pagesize/d'))->toArray();
        if(count($page['Rows'])){
            foreach ($page['Rows'] as $key => $v) {
                $page['Rows'][$key]['commissionFee'] = abs($v['commissionFee']);
                $page['Rows'][$key]['payTypeNames'] = WSTLangPayType($v['payType']);
            }
        }
        return $page;
    }

    /**
     * 获取结算订单详情
     */
    public function getById(){
        $settlementId = (int)input('id');
        $object =  Db::name('settlements')->alias('st')->where('settlementId',$settlementId)->join('__SHOPS__ s','s.shopId=st.shopId','left')->field('s.shopName,st.*')->find();
        if(!empty($object)){
            $object['list'] = Db::name('orders')->where(['settlementId'=>$settlementId])
                      ->field('orderId,orderNo,payType,goodsMoney,deliverMoney,realTotalMoney,totalMoney,commissionFee,createTime')
                      ->order('payType desc,orderId desc')->select();
        }
        return $object;
    }
}