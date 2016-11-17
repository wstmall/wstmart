<?php
namespace wstmart\common\model;
use wstmart\home\model\Shops;
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
 * 商城消息
 */
class Messages extends Base{
   /**
    * 获取列表
    */
    public function pageQuery(){
      	 $userId = (int)session('WST_USER.userId');
         $where = ['receiveUserId'=>(int)$userId,'dataFlag'=>1];
         $page = model('Messages')->where($where)->order('msgStatus asc,id desc')->paginate(input('pagesize/d'))->toArray();
         foreach ($page['Rows'] as $key => $v){
         	$page['Rows'][$key]['msgContent'] = WSTMSubstr(strip_tags($v['msgContent']),0,140);
         }
         return $page;
    }
   /**
    *  获取某一条消息详情
    */
    public function getById(){
    	$userId = (int)session('WST_USER.userId');
        $id = (int)input('msgId');
        $data = $this->get(['id'=>$id,'receiveUserId'=>$userId]);
        if(!empty($data)){
          if($data['msgStatus']==0)
            model('Messages')->where('id',$id)->setField('msgStatus',1);
        }
        return $data;
    }

    /**
     * 删除
     */
    public function del(){
    	$userId = (int)session('WST_USER.userId');
        $id = input('id/d');
        $data = [];
        $data['dataFlag'] = -1;
        $result = $this->update($data,['id'=>$id,'receiveUserId'=>$userId]);
        if(false !== $result){
            return WSTReturn("删除成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }
    /**
    * 批量删除
    */
    public function batchDel(){
    	$userId = (int)session('WST_USER.userId');
        $ids = input('ids/a');
        $data = [];
        $data['dataFlag'] = -1;
        $result = $this->update($data,['id'=>['in',$ids],'receiveUserId'=>$userId]);
        if(false !== $result){
            return WSTReturn("删除成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }
    /**
    * 标记为已读
    */
    public function batchRead(){
    	$userId = (int)session('WST_USER.userId');
        $ids = input('ids/a');
        $data = [];
        $data['msgStatus'] = 1;
        $result = $this->update($data,['id'=>['in',$ids],'receiveUserId'=>$userId]);
        if(false !== $result){
            return WSTReturn("操作成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }

    
}
