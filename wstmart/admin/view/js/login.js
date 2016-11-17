function login(){
	var loading = WST.msg('加载中', {icon: 16,time:60000});
	var params = WST.getParams('.ipt');
	$.post(WST.U('admin/index/checkLogin'),params,function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
			WST.msg("登录成功",{icon:1},function(){
				location.href=WST.U('admin/index/index');
			});
		}else{
			getVerify('#verifyImg');
			WST.msg(json.msg,{icon:2});			
		}
	});
}
getVerify = function(img){
	$(img).attr('src',WST.U('admin/index/getVerify','rnd='+Math.random()));
}