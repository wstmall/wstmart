var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/staffs/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '职员账号', name: 'loginName',isSort: false},
	        { display: '职员名称', name: 'staffName',isSort: false},
	        { display: '职员角色', name: 'roleName',isSort: false},
	        { display: '职员编号', name: 'staffNo',isSort: false},
	        { display: '工作状态', name: 'workStatus',isSort: false,render: function (rowdata, rowindex, value){
	        	return (value==1)?"在职":"离职";
	        }},
	        { display: '登录时间', name: 'lastTime',isSort: false},
	        { display: '登录IP', name: 'lastIP',isSort: false},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(WST.GRANT.ZYGL_02)h += "<a href='javascript:toEditPass(" + rowdata['staffId'] + ")'>修改密码</a> ";
	            if(WST.GRANT.ZYGL_02)h += "<a href='javascript:toEdit(" + rowdata['staffId'] + ")'>修改</a> ";
	            if(WST.GRANT.ZYGL_03)h += "<a href='javascript:toDel(" + rowdata['staffId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });
}
function loadGrid(){
	grid.set('url',WST.U('admin/staffs/pageQuery','key='+$('#key').val()));
}
function toEdit(id){
	location.href=WST.U('admin/staffs/'+((id==0)?'toAdd':'toEdit'),'id='+id);
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该职员吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           $.post(WST.U('admin/staffs/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
	           		            grid.reload();
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}
function checkLoginKey(obj){
	if($.trim(obj.value)=='')return;
	var params = {key:obj.value,userId:0};
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/staffs/checkLoginKey'),params,function(data,textStatus){
    	layer.close(loading);
    	var json = WST.toAdminJson(data);
    	if(json.status!='1'){
    		WST.msg(json.msg,{icon:2});
    		obj.value = '';
    	}
    });
}
function save(){
	var params = WST.getParams('.ipt');
	if(params.staffId==0){
		if(!$('#loginName').isValid())return;
		if(!$('#loginPwd').isValid())return;
	}
	if(!$('#staffName').isValid())return;
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/staffs/'+((params.staffId==0)?"add":"edit")),params,function(data,textStatus){
    	layer.close(loading);
    	var json = WST.toAdminJson(data);
    	if(json.status=='1'){
    		WST.msg("操作成功",{icon:1});
    		location.href=WST.U('admin/staffs/index');
    	}else{
    		WST.msg(json.msg,{icon:2});
    	}
    });
}
function toEditPass(id){
	var w = WST.open({type: 1,title:"修改密码",shade: [0.6, '#000'],border: [0],content:$('#editPassBox'),area: ['450px', '200px'],
	    btn: ['确定', '取消'],yes: function(index, layero){
	    	$('#editPassFrom').isValid(function(v){
	    		if(v){
		        	var params = WST.getParams('.ipt');
		        	params.staffId = id;
		        	var ll = WST.msg('数据处理中，请稍候...');
				    $.post(WST.U('admin/Staffs/editPass'),params,function(data){
				    	layer.close(ll);
				    	var json = WST.toAdminJson(data);
						if(json.status==1){
							WST.msg(json.msg, {icon: 1});
							layer.close(w);
						}else{
							WST.msg(json.msg, {icon: 2});
						}
				   });
	    		}})
        }
	});
}
