<?php
namespace wstmart\common\model;
use Think\Db;
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
 * 用户类
 */
class Users extends Base{
    /**
     * 用户登录验证
     */
    public function checkLogin(){
    	$loginName = input("post.loginName");
    	$loginPwd = input("post.loginPwd");
    	$code = input("post.verifyCode");
    	$rememberPwd = input("post.rememberPwd",1);
    	if(!WSTVerifyCheck($code) && strpos(WSTConf("CONF.captcha_model"),"4")>=0){
    		return WSTReturn('验证码错误!');
    	}
    	$rs = $this->where("loginName|userEmail|userPhone",$loginName)
    				->where(["dataFlag"=>1, "userStatus"=>1])
    				->find();
    	if(!empty($rs)){
    		$userId = $rs['userId'];
    		//获取用户等级
	    	$rrs = Db::name('user_ranks')->where('startScore','<=',$rs['userTotalScore'])->where('endScore','>=',$rs['userTotalScore'])->field('rankId,rankName,rebate,userrankImg')->find();
	    	$rs['rankId'] = $rrs['rankId'];
	    	$rs['rankName'] = $rrs['rankName'];
	    	$rs['userrankImg'] = $rrs['userrankImg'];
    		if(input("post.typ")==2){
    			$shoprs=$this->where(["dataFlag"=>1, "userStatus"=>1,"userType"=>1,"userId"=>$userId])->find();
    			if(empty($shoprs)){
    				return WSTReturn('您还没申请店铺!');
    			}
    		}
    		if($rs['loginPwd']!=md5($loginPwd.$rs['loginSecret']))return WSTReturn("密码错误");
    		$ip = request()->ip();
    		$this->where(["userId"=>$userId])->update(["lastTime"=>date('Y-m-d H:i:s'),"lastIP"=>$ip]);
    		//如果是店铺则加载店铺信息
    		if($rs['userType']>=1){
    			$shop = model('shops')->where(["userId"=>$userId,"dataFlag" =>1])->find();
    			if(!empty($shop))$rs = array_merge($shop->toArray(),$rs->toArray());
    		}
    		//记录登录日志
    		$data = array();
    		$data["userId"] = $userId;
    		$data["loginTime"] = date('Y-m-d H:i:s');
    		$data["loginIp"] = $ip;
    		Db::name('log_user_logins')->insert($data);
    
    		$rd = $rs;
    		//记住密码
    		cookie("loginName", $loginName, time()+3600*24*90);
    		if($rememberPwd == "on"){
    			$datakey = md5($rs['loginName'])."_".md5($rs['loginPwd']);
    			$key = $rs['loginSecret'];
    			//加密
    			$base64 = new \org\Base64();
    			$loginKey = $base64->encrypt($datakey, $key);
    			cookie("loginPwd", $loginKey, time()+3600*24*90);
    		}else{
    			cookie("loginPwd", null);
    		}
    		session('WST_USER',$rs);
    		return WSTReturn("","1");
    	}
    	return WSTReturn("用户不存在");
    }
    
    /**
     * 会员注册
     */
    public function regist(){
    	
    	$data = array();
    	$data['loginName'] = input("post.loginName");
    	$data['loginPwd'] = input("post.loginPwd");
    	$data['reUserPwd'] = input("post.reUserPwd");
    	$loginName = $data['loginName'];
    	//检测账号是否存在
    	$crs = WSTCheckLoginKey($loginName);
    	if($crs['status']!=1)return $crs;
    	if($data['loginPwd']!=$data['reUserPwd']){
    		return WSTReturn("两次输入密码不一致!");
    	}
    	foreach ($data as $v){
    		if($v ==''){
    			return WSTReturn("注册信息不完整!");
    		}
    	}
    	$nameType = (int)input("post.nameType");
    	$mobileCode = input("post.mobileCode");
    	$code = input("post.verifyCode");
    	if($nameType!=3 && !WSTVerifyCheck($code)){
    		return WSTReturn("验证码错误!");
    	}
    	if($nameType==3 && WSTConf("CONF.phoneVerfy")==1){//手机号码
    		$data['userPhone'] = $loginName;
    		$verify = session('VerifyCode_userPhone');
    		$startTime = (int)session('VerifyCode_userPhone_Time');
    		if((time()-$startTime)>120){
    			return WSTReturn("验证码已超过有效期!");
    		}
    		if($mobileCode=="" || $verify != $mobileCode){
    			return WSTReturn("验证码错误!");
    		}
    		$loginName = WSTRandomLoginName($loginName);
    	}else if($nameType==1){//邮箱注册
    		$data['userEmail'] = $loginName;
    		$unames = explode("@",$loginName);
    		$loginName = WSTRandomLoginName($unames[0]);
    		
    	}
    	if($loginName=='')return WSTReturn("注册失败!");//分派不了登录名
    	$data['loginName'] = $loginName;
    	unset($data['reUserPwd']);
    	unset($data['protocol']);
    	//检测账号，邮箱，手机是否存在
    	$data["loginSecret"] = rand(1000,9999);
    	$data['loginPwd'] = md5($data['loginPwd'].$data['loginSecret']);
    	$data['userType'] = 0;
    	$data['userName'] = input("post.userName");
    	$data['userQQ'] = "";
    	$data['userScore'] = 0;
    	$data['createTime'] = date('Y-m-d H:i:s');
    	$data['dataFlag'] = 1;
    	Db::startTrans();
        try{
	    	$userId = $this->data($data)->save();
	    	if(false !== $userId){
	    		$data = array();
	    		$ip = request()->ip();
	    		$data['lastTime'] = date('Y-m-d H:i:s');
	    		$data['lastIP'] = $ip;
	    		$userId = $this->userId;
	    		$this->where(["userId"=>$userId])->update($data);
	    		//记录登录日志
	    		$data = array();
	    		$data["userId"] = $userId;
	    		$data["loginTime"] = date('Y-m-d H:i:s');
	    		$data["loginIp"] = $ip;
	    		Db::name('log_user_logins')->insert($data);
	    		$user = $this->get($userId);
	    		session('WST_USER',$user);
	    		Db::commit();
	    		return WSTReturn("",1);
	    	}
        }catch (\Exception $e) {
        	Db::rollback();
        }
    	return WSTReturn("注册失败!");
    }
    
    /**
     * 查询用户手机是否存在
     * 
     */
    public function checkUserPhone($userPhone,$userId = 0){
    	$dbo = $this->where(["dataFlag"=>1, "userPhone"=>$userPhone]);
    	if($userId>0){
    		$dbo->where("userId","<>",$userId);
    	}
    	$rs = $dbo->count();
    	if($rs>0){
    		return WSTReturn("手机号已存在!");
    	}else{
    		return WSTReturn("",1);
    	}
    }

    /**
     * 修改用户密码
     */
    public function editPass($id){
    	$data = array();
    	$data["loginPwd"] = input("post.newPass");
    	if(!$data["loginPwd"]){
    		return WSTReturn('密码不能为空',-1);
    	}
    	$rs = $this->where('userId='.$id)->find();
    	//核对密码
    	if($rs['loginPwd']){
    		if($rs['loginPwd']==md5(input("post.oldPass").$rs['loginSecret'])){
    			$data["loginPwd"] = md5(input("post.newPass").$rs['loginSecret']);
    			$rs = $this->update($data,['userId'=>$id]);
    			if(false !== $rs){
    				return WSTReturn("密码修改成功", 1);
    			}else{
    				return WSTReturn($this->getError(),-1);
    			}
    		}else{
    			return WSTReturn('原始密码错误',-1);
    		}
    	}else{
    		$data["loginPwd"] = md5(input("post.newPass").$rs['loginSecret']);
    		$rs = $this->update($data,['userId'=>$id]);
    		if(false !== $rs){
    			return WSTReturn("密码修改成功", 1);
    		}else{
    			return WSTReturn($this->getError(),-1);
    		}
    	}
    }
    /**
     * 修改用户支付密码
     */
    public function editPayPass($id){
        $data = array();
        $data["payPwd"] = input("post.newPass");
        if(!$data["payPwd"]){
            return WSTReturn('支付密码不能为空',-1);
        }
        $rs = $this->where('userId='.$id)->find();
        //核对密码
        if($rs['payPwd']){
            if($rs['payPwd']==md5(input("post.oldPass").$rs['loginSecret'])){
                $data["payPwd"] = md5($data["payPwd"].$rs['loginSecret']);
                $rs = $this->update($data,['userId'=>$id]);
                if(false !== $rs){
                    return WSTReturn("支付密码修改成功", 1);
                }else{
                    return WSTReturn("支付密码修改失败",-1);
                }
            }else{
                return WSTReturn('原始支付密码错误',-1);
            }
        }else{
            $data["payPwd"] = md5($data["payPwd"].$rs['loginSecret']);
            $rs = $this->update($data,['userId'=>$id]);
            if(false !== $rs){
                return WSTReturn("支付密码设置成功", 1);
            }else{
                return WSTReturn("支付密码修改失败",-1);
            }
        }
    }
   /**
    *  获取用户信息
    */
    public function getById($id){
    	$rs = $this->get(['userId'=>(int)$id]);
    	$rs['ranks'] = Db::name('user_ranks')->where('startScore','<=',$rs['userTotalScore'])->where('endScore','>=',$rs['userTotalScore'])->field('rankId,rankName,rebate,userrankImg')->find();
    	return $rs;
    }
    /**
     * 编辑资料
    */
    public function edit(){
    	$Id = (int)session('WST_USER.userId');
    	$data = input('post.');
        $data['brithday'] = ($data['brithday']=='')?date('Y-m-d'):$data['brithday'];
    	WSTAllow($data,'brithday,trueName,userName,userId,userPhoto,userQQ,userSex');
    	Db::startTrans();
		try{
            if(isset($data['userPhoto']))
			     WSTUseImages(0, $Id, $data['userPhoto'],'users','userPhoto');
             
	    	$result = $this->allowField(true)->save($data,['userId'=>$Id]);
	    	if(false !== $result){
	    		Db::commit();
	    		return WSTReturn("编辑成功", 1);
	    	}
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }	
    }
    /**
    * 绑定邮箱
     */
    public function editEmail($userId,$userEmail){
    	$data = array();
    	$data["userEmail"] = $userEmail;
		$rs = $this->update($data,['userId'=>$userId]);
		if(false !== $rs){
			return WSTReturn("",1);
		}else{
			return WSTReturn("",-1);
		}
    }
    /**
     * 绑定手机
     */
    public function editPhone($userId,$userPhone){
    	$data = array();
    	$data["userPhone"] = $userPhone;
    	$rs = $this->update($data,['userId'=>$userId]);
    	if(false !== $rs){
    		return WSTReturn("绑定成功", 1);
    	}else{
    		return WSTReturn($this->getError(),-1);
    	}
    }
    /**
     * 查询并加载用户资料
     */
    public function checkAndGetLoginInfo($key){
    	if($key=='')return array();
    	$rs = $this->where(["loginName|userEmail|userPhone"=>['=',$key],'dataFlag'=>1])->find();
    	return $rs;
    }
    /**
     * 重置用户密码
     */
    public function resetPass(){
    	if(time()>floatval(session('REST_Time'))+30*60){
    		return WSTReturn("连接已失效！", -1);
    	}
    	$reset_userId = (int)session('REST_userId');
    	if($reset_userId==0){
    		return WSTReturn("无效的用户！", -1);
    	}
    	$user = $this->where(["dataFlag"=>1,"userStatus"=>1,"userId"=>$reset_userId])->find();
    	if(empty($user)){
    		return WSTReturn("无效的用户！", -1);
    	}
    	$loginPwd = input("post.loginPwd");
    	if(trim($loginPwd)==''){
    		return WSTReturn("无效的密码！", -1);
    	}
    	$data['loginPwd'] = md5($loginPwd.$user["loginSecret"]);
    	$rc = $this->update($data,['userId'=>$reset_userId]);
    	if(false !== $rc){
    		return WSTReturn("修改成功", 1);
    	}
    	session('REST_userId',null);
    	session('REST_Time',null);
    	session('REST_success',null);
    	session('findPass',null);
    	return $rs;
    }
    
    /**
     * 获取用户可用积分
     */
    public function getFieldsById($userId,$fields){
    	return $this->where(['userId'=>$userId,'dataFlag'=>1])->field($fields)->find();
    }
}
