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
 * 商家认证业务处理
 */
class Accreds extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		return $this->where('dataFlag',1)->field(true)->order('accredId desc')->paginate(input('pagesize/d'));
	}
	/**
	 * 列表
	 */
    public function listQuery(){
		return $this->where('dataFlag',1)->field(true)->select();
	}
	public function getById($id){
		return $this->get(['accredId'=>$id,'dataFlag'=>1]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['createTime'] = date('Y-m-d H:i:s');
		WSTUnset($data,'accredId');
		Db::startTrans();
		try{
			$result = $this->validate('Accreds.add')->allowField(true)->save($data);
			if(false !==$result){
				$id = $this->accredId;
				//启用上传图片
				WSTUseImages(1, $id, $data['accredImg']);
		        if(false !== $result){
		        	Db::commit();
		        	return WSTReturn("新增成功", 1);
		        }
			}
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('新增失败',-1);	
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$data = input('post.');
		WSTUnset($data,'createTime');
		Db::startTrans();
		try{
			WSTUseImages(1, (int)$data['accredId'], $data['accredImg'], 'accreds', 'accredImg');
		    $result = $this->validate('Accreds.edit')->allowField(true)->save($data,['accredId'=>(int)$data['accredId']]);
	        if(false !== $result){
	        	Db::commit();
	        	return WSTReturn("编辑成功", 1);
	        }
	    }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('编辑失败',-1);  
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id/d');
	    Db::startTrans();
		try{
		    $result = $this->setField(['dataFlag'=>-1,'accredId'=>$id]);
		    WSTUnuseImage('accreds','accredImg',$id);	
	        if(false !== $result){
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1); 
	}
	
}
