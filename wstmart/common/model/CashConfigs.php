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
 * 提现账号业务处理器
 */
class CashConfigs extends Base{
     /**
      * 获取列表
      */
      public function pageQuery($targetType,$targetId){
      	  $type = (int)input('post.type',-1);
          $where = [];
          $where['targetType'] = (int)$targetType;
          $where['targetId'] = (int)$targetId;
          $where['c.dataFlag'] = 1;
          if(in_array($type,[0,1]))$where['moneyType'] = $type;
          $page = $this->alias('c')->join('__BANKS__ b','c.accTargetId=b.bankId')->where($where)->field('b.bankName,c.*')->order('c.id desc')->paginate()->toArray();
          if(count($page['Rows'])>0){
              foreach($page['Rows'] as $key => $v){
                  $areas = model('areas')->getParentNames($v['accAreaId']);
                  $page['Rows'][$key]['areaName'] = implode('',$areas);
              }
          }
          return $page;
      }
      /**
       * 获取列表
       */
      public function listQuery($targetType,$targetId){
          $where = [];
          $where['targetType'] = (int)$targetType;
          $where['targetId'] = (int)$targetId;
          $where['dataFlag'] = 1;
          return $this->where($where)->field('id,accNo,accUser')->select();
      }
      /**
       * 获取资料
       */
      public function getById($id){
          $config = $this->get($id);
          $areas = model('areas')->getParentIs($config['accAreaId']);
          $config['accAreaIdPath'] = implode('_',$areas)."_";
          return $config;
      }
      /**
       * 新增卡号
       */
      public function add(){
          $data = input('post.');
          $data['targetType'] = 0;
          $data['targetId'] = (int)session('WST_USER.userId');
          $data['accType'] = 3; 
          $data['userId'] = (int)session('WST_USER.userId');
          $data['createTime'] = date('Y-m-d H:i:s');
          WSTUnset($data,'id');
          $result = $this->validate('CashConfigs.add')->allowField(true)->save($data);
          if(false !== $result){
              return WSTReturn("新增成功", 1,['id'=>$this->id]);
          }else{
              return WSTReturn($this->getError(),-1);
          }
      }
      /**
       * 编辑卡号
       */
      public function edit(){
          $id = (int)input('id');
          $data = input('post.');
          $userId = (int)session('WST_USER.userId');
          WSTUnset($data,'id,targetType,targetId,accType,createTime');
          $result = $this->validate('CashConfigs.edit')->allowField(true)->save($data,['id'=>$id,'targetId'=>$userId]);
          if(false !== $result){
              return WSTReturn("编辑成功", 1);
          }else{
              return WSTReturn($this->getError(),-1);
          }
      }
      /**
       *  删除提现账号
       */
      public function del(){
         $object = $this->get((int)input('id'));
         $object->dataFlag = -1;
         $result = $object->save();
         if(false !== $result){
            return WSTReturn('操作成功',1);
         }
         return WSTReturn('操作失败',-1);
      }
}
