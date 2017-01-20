var time = 0;
var isSend = false;
$(function(){
    //第一步
    $('#forgetPwdForm').validator({
      valid: function(form){
    	  forgetPwd();
      }
    });
   //手机发送验证
    $('#phoneVerify').validator({
        valid: function(form){
      	  phoneVerify2();
        }
      });
    //重置密码
    $('#forgetPwdForm3').validator({
        fields: {
        	loginPwd: {
              rule:"required;length[6~16]",
              msg:{required:"请输入新密码"},
              tip:"请输入新密码"
            },
            repassword: {
              rule:"required;length[6~16];match[loginPwd]",
              msg:{required:"请再次输入新密码",match:"两次输入密码不匹配"},
              tip:"请再次输入新密码"
            },
        },
        valid: function(form){
        	forgetPwd();
        }
    });
})
function forgetPwd(){
    var params = WST.getParams('.ipt');
    var step = $('#step').val();
    var modes = $('#modes').val();
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('home/users/findPass'),params,function(data,textStatus){
      layer.close(loading);
      var json = WST.toJson(data);
      if(json.status=='1'){
    	WST.msg("操作成功",{icon:1});
	        setTimeout(function(){
	        	if(step==1){
		        	location.href=WST.U('home/users/forgetPasst');
	        	}else if(step==2){
	        		if(modes==1){
	        			location.href=json.url;
	        		}else{
	        			disableBtn();
	        		}
	        	}else if(step==3){
	        		location.href=WST.U('home/users/forgetPassf');
	        	}
	        },1000);
      }else{
            WST.msg(json.msg,{icon:2});
            WST.getVerify('#verifyImg');
      }
    });
}

//第二步
$('#type').change(function(){
    if ($('#type').val() == 'phone') {
        $('.phone-verify').show();
        $('.email-verify').hide();
        $('#modes').val(1);
    }else{
        $('.phone-verify').hide();
        $('.email-verify').show();
        $('#modes').val(2);
    }
})
function phoneVerify(){
	if(window.conf.SMS_VERFY==1){
		WST.open({type: 1,title:"请输入验证码",shade: [0.6, '#000'],border: [0],content: $('#phoneVerify'),area: ['500px', '160px']});
	}else{
		phoneVerify2();
	}
}
function phoneVerify2(){
	WST.msg('正在发送短信，请稍后...',{time:600000});
	var time = 0;
	var isSend = false;
	var params = WST.getParams('.ipt');
	$.post(WST.U('home/users/getfindPhone'),params,function(data,textStatus){
		var json = WST.toJson(data);
		if(isSend )return;
		isSend = true;
		if(json.status!=1){
			WST.msg(json.msg, {icon: 5});
			WST.getVerify2('#verifyImg2');
			time = 0;
			isSend = false;
		}if(json.status==1){
			WST.msg('短信已发送，请注册查收');
			layer.closeAll('page'); 
			time = 120;
			$('#timeObtain').attr('disabled', 'disabled').css('background','#e8e6e6');
			$('#timeObtain').html('获取手机验证码(120)').css('width','130px');
			var task = setInterval(function(){
				time--;
				$('#timeObtain').html('获取手机验证码('+time+")");
				if(time==0){
					isSend = false;						
					clearInterval(task);
					$('#timeObtain').html("重新获取验证码").css('width','100px');
					$('#timeObtain').removeAttr('disabled').css('background','#e23e3d');
				}
			},1000);
		}
	});
}
function forgetPhone(){
	if(!$('#Checkcode').isValid())return;
	forgetPwd();
}
function forgetEmail(){
	if(!$('#verifyCode').isValid())return;
	forgetPwd();
}
/*重置密码*/
function resetPass(){
	if(!$('#secretCode').isValid())return;
	var secretCode = $('#secretCode').val();
	$.post(WST.U('home/users/forgetPasss'),{secretCode:secretCode},function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			location.href=WST.U('home/users/resetPass');
		}else{
			WST.msg(json.msg,{icon:2});
			return false;
		}
	})
}

/*禁用发送按钮*/
function disableBtn(){
	time = 120;
	$('#sendEmailBtn').attr('disabled', 'disabled').css({'background':'#e8e6e6','color':'#a7a7a7'});
	$('#sendEmailBtn').html('获取邮箱验证码(120)').css('width','130px');
	var task = setInterval(function(){
		time--;
		$('#sendEmailBtn').html('获取邮箱验证码('+time+")");
		if(time==0){
			isSend = false;						
			clearInterval(task);
			$('#sendEmailBtn').html("重新获取验证码").css('width','100px');
			$('#sendEmailBtn').removeAttr('disabled').css({'background':'#f0efef','color':'#110f0f'});
		}
	},1000);
}