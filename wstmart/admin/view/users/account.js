var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/users/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '账号', name: 'loginName', isSort: false},
	        { display: '用户名', name: 'userName', isSort: false},
	        { display: '手机号码', name: 'userPhone', isSort: false},
	        { display: '电子邮箱', name: 'userEmail', isSort: false},
	        { display: '最后登录时间', name: 'lastTime', isSort: false},
	        { display: '状态', name: 'userStatus', isSort: false, render:function(rowdata, rowindex, value){
	        	return (value==1)?'<span style="cursor:pointer;" onclick="changeUserStatus('+rowdata['userId']+',0)">启用</span>':'<span style="cursor:pointer;" onclick="changeUserStatus('+rowdata['userId']+',1)">停用</span>';
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(WST.GRANT.ZHGL_02)h += "<a href='javascript:getForEdit(" + rowdata['userId'] + ")'>修改</a> ";
	            if(WST.GRANT.ZHGL_02)h += "<a href='javascript:resetPayPwd(" + rowdata['userId'] + ")'>重置支付密码</a> ";
	            return h;
	        }}
        ]
    });
}

function resetPayPwd(id){
	var box = WST.confirm({content:"您确定重置支付密码为666666吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/users/resetPayPwd'),{userId:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("重置成功",{icon:1});
	           			    	layer.close(box);
	           		            grid.reload();
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}
function getForEdit(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
     $.post(WST.U('admin/users/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           //清空密码
           json.loginPwd = '';
           if(json.userId){
           		WST.setValues(json);
           		$('#userId').val(json.userId);
           		toEdit(json.userId);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
	var box = WST.open({title:'编辑',type:1,content:$('#accountBox'),area: ['450px', '260px'],btn:['确定','取消'],yes:function(){
					$('#accountForm').isValid(function(v){
						if(v){
							var params = WST.getParams('.ipt');
			                if(id>0)
			                	params.userId = id;
			                var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
			           		$.post(WST.U('admin/users/editAccount'),params,function(data,textStatus){
			           			  layer.close(loading);
			           			  var json = WST.toAdminJson(data);
			           			  if(json.status=='1'){
			           			    	WST.msg("操作成功",{icon:1});
			           			    	$('#accountForm')[0].reset();
			           			    	layer.close(box);
			           		            grid.reload();
			           			  }else{
			           			        WST.msg(json.msg,{icon:2});
			           			  }
			           		});
						}else{
							return false;
						}
					});
		        	
		

	},cancel:function(){$('#accountForm')[0].reset();},end:function(){$('#accountForm')[0].reset();}});

}

function changeUserStatus(id, status){
	if(!WST.GRANT.ZHGL_02)return;
	$.post(WST.U('admin/Users/changeUserStatus'), {'id':id, 'status':status}, function(data, textStatus){
		var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           		            grid.reload();
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	})
}


function accountQuery(){
          var query = WST.getParams('.query');
			    grid.set('url',WST.U('admin/Users/pageQuery',query));
			}

		