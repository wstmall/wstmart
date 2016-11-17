var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/roles/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '权限名称', name: 'roleName'},
	        { display: '权限说明', name: 'roleDesc'},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(WST.GRANT.JSGL_02)h += "<a href='javascript:toEdit(" + rowdata['roleId'] + ")'>修改</a> ";
	            if(WST.GRANT.JSGL_03)h += "<a href='javascript:toDel(" + rowdata['roleId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });
}
function toEdit(id){
	location.href=WST.U('admin/roles/toEdit','id='+id);
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该角色吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           $.post(WST.U('admin/roles/del'),{id:id},function(data,textStatus){
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
function getNodes(event, treeId, treeNode){
	zTree.expandNode(treeNode,true, true, true);
	if($.inArray(treeNode.privilegeCode,rolePrivileges)>-1){
		zTree.checkNode(treeNode,true,true);
	}
}
function save(){
	if(!$('#roleName').isValid())return;
	var nodes = zTree.getChangeCheckedNodes();
	var privileges = [];
	for(var i=0;i<nodes.length;i++){
		if(nodes[i].isParent==0)privileges.push(nodes[i].privilegeCode);
	}
	var params = WST.getParams('.ipt');
	params.privileges = privileges.join(',');
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/roles/'+((params.roleId==0)?"add":"edit")),params,function(data,textStatus){
    	layer.close(loading);
    	var json = WST.toAdminJson(data);
    	if(json.status=='1'){
    		WST.msg("操作成功",{icon:1});
    		location.href=WST.U('admin/roles/index');
    	}else{
    		WST.msg(json.msg,{icon:2});
    	}
    });
}
