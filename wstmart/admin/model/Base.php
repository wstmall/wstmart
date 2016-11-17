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
 * 基础控业务处理
 */
use think\Model;
use think\Db;
class Base extends Model {
    /**
     * 获取空模型
     */
    public function getEModel($tables){
    	$rs =  Db::query('show columns FROM `'.config('database.prefix').$tables."`");
        $obj = [];
        if($rs){
            foreach($rs as $key => $v) {
                $obj[$v['Field']] = $v['Default'];
                if($v['Key'] == 'PRI')$obj[$v['Field']] = 0;
            }
        }
        return $obj;
    }
}