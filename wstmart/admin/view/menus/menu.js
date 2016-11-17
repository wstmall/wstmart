var zTree,grid;
$(function(){
	$("#layout").ligerLayout({leftWidth:'230',space: 8,allowLeftCollapse:false,allowCenterBottomResize:false});
	$('#menuTree').height(WST.pageHeight()-36);
	var setting = {
	      view: {
	           selectedMulti: false,
	           dblClickExpand:false
	      },
	      async: {
	           enable: true,
	           url:WST.U('admin/menus/listQuery'),
	           autoParam:["id", "name=n", "level=lv"]
	      },
	      callback:{
	           onRightClick: onRightClick,
	           onClick: onClick,
	           onAsyncSuccess: onAsyncSuccess
	      }
	};
	$.fn.zTree.init($("#menuTree"), setting);
	zTree = $.fn.zTree.getZTreeObj("menuTree");
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/privileges/listQuery'),
		pageSize:100,
		pageSizeOptions:[100],
		height:'99%',
        width:'100%',
        minColToggle:6,
        delayLoad:true,
        rownumbers:true,
        columns: [
	        { display: '权限名称', name: 'privilegeName'},
	        { display: '权限代码', name: 'privilegeCode'},
	        { display: '是否菜单权限', name: 'isMenuPrivilege',render: function (rowdata, rowindex, value){
	            return value==1?"是":"否";
	        }},
	        { display: '权限资源', name: 'privilegeUrl'},
	        { display: '关联资源', name: 'otherPrivilegeUrl'},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(WST.GRANT.QXGL_02)h += "<a href='javascript:getForEdit(" + rowdata['privilegeId'] + ")'>修改</a> ";
	            if(WST.GRANT.QXGL_03)h += "<a href='javascript:toDel(" + rowdata['privilegeId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });
})

function onAsyncSuccess(event, treeId, treeNode, msg){
	var json = WST.toAdminJson(msg);
	if(json && json.id==0){
		var treeNode = zTree.getNodeByTId('menuTree_1');
		zTree.reAsyncChildNodes(treeNode, "refresh",true);
		zTree.expandAll(treeNode,true);
	}
}
function onClick(e,treeId, treeNode){
	if(treeNode.id>0){
	      $('.wst-toolbar').show();
	      $('#maingrid').show();
	}else{
	    $('.wst-toolbar').hide();
	    $('#maingrid').hide();
	}
	grid.setParm('id',treeNode.id);
	grid.reload();
}
function onRightClick(event, treeId, treeNode) {
	if(!treeNode)return;
	$('#'+treeNode.tId).bind('contextmenu',function(e){
		 var items = [];
		 if(WST.GRANT.CDGL_01)items.push({
			 icon : 'add',
             text : '新增菜单',
             click: function(parent, menu) {
            	   treeNode = zTree.getSelectedNodes()[0];
            	   editMenu({menuId:0,menuName:'',parentId:treeNode.id,pnode:treeNode,menuSort:0});
             }});
		 treeNode = zTree.getSelectedNodes()[0];
		 if(treeNode.id>0){
			 if(WST.GRANT.CDGL_02)items.push({
                 icon  : 'pencil',
                 text  : '编辑菜单',
                 click : function(parent, menu) {
                  	treeNode = zTree.getSelectedNodes()[0];
                  	getForEditMenu(treeNode.id);
             }});
			 if(WST.GRANT.CDGL_03)items.push({
                 icon  : 'remove',
                 text  : '删除菜单',
                 click : function(parent, menu) {
                  	treeNode = zTree.getSelectedNodes()[0];
                  	layer.confirm('您确定要删除该菜单['+treeNode.name+']吗？', {btn: ['确定','取消']}, function(){
                  	    var loading = WST.msg('正在提交请求，请稍后...', {icon: 16,time:60000});
                  	    $.post(WST.U('admin/menus/del'),{id:treeNode.id},function(data,textStatus){
                  		     layer.close(loading);
                  		     var json = WST.toAdminJson(data);
                  		     if(json.status=='1'){
                  		          WST.msg("操作成功",{icon:1});
                  		          zTree.reAsyncChildNodes(treeNode.getParentNode(), "refresh",true);
                  		     }else{
                  		          WST.msg(json.msg,{icon:2});
                  		     }
                  		 });
                      });
             }});
		 }
	     var menu = $.ligerMenu({ top: 100, left: 100, width: 120, items:items});
	     menu.show({ top: e.pageY, left: e.pageX });
	     return false;
	});
}
function getForEditMenu(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/menus/get'),{id:id},function(data,textStatus){
          layer.close(loading);
          var json = WST.toAdminJson(data);
          if(json.menuId){
          	  editMenu(json);
          }else{
          	  WST.msg(json.msg,{icon:2});
          }
   });
}	                    		
function editMenu(obj){
	WST.setValues(obj);
	var box = WST.open({ title:(obj.menuId==0)?'新增菜单':"编辑菜单",type: 1,area: ['430px', '190px'],
	                content:$('#menuBox'),
	                btn:['确定','取消'],
	                yes: function(index, layero){
	                	if(!$('#menuName').isValid())return;
		                var params = WST.getParams('.ipt2');
		                params.menuId = obj.menuId;
		                var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		                $.post(WST.U('admin/menus/'+((params.menuId)?"edit":"add")),params,function(data,textStatus){
		                	layer.close(loading);
		                	var json = WST.toAdminJson(data);
		                	if(json.status=='1'){
		                	    WST.msg("操作成功",{icon:1});
		                		layer.close(box);
		                	    $('#menuForm')[0].reset();
		                		treeNode = zTree.getSelectedNodes()[0];
		                		if(params.menuId){
			                		zTree.reAsyncChildNodes(treeNode.getParentNode(), "refresh",true);
		                	    }else{
		                			zTree.reAsyncChildNodes(treeNode, "refresh",true);
		                		}
		                	}else{
		                			WST.msg(json.msg,{icon:2});
		                	}
		                });
	            }});
}

function getForEdit(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
     $.post(WST.U('admin/privileges/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.privilegeId){
           		WST.setValues(json);
           		toEdit(json.privilegeId);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
	var title =(id==0)?"新增权限":"编辑权限";
	var box = WST.open({title:title,type:1,content:$('#privilegeBox'),area: ['450px', '320px'],btn:['确定','取消'],yes:function(){
		            if(!$('#privilegeName').isValid())return;
		            if(!$('#privilegeCode').isValid())return;
	                var params = WST.getParams('.ipt');
	                params.menuId = zTree.getSelectedNodes()[0].id;
	                params.id = id;
	                var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           		$.post(WST.U('admin/privileges/'+((id==0)?"add":"edit")),params,function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	$('#privilegeForm')[0].reset();
	           			    	layer.close(box);
	           		            grid.reload();
	           			  }else{
	           			        WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	          }});
}

function toDel(id){
	var box = WST.confirm({content:"您确定要删除该权限吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/privileges/del'),{id:id},function(data,textStatus){
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
function checkPrivilegeCode(obj){
	if($.trim(obj.value)=='')return;
	var loading = WST.msg('正在检测代码是否存在，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/privileges/checkPrivilegeCode'),{code:obj.value},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status!='1'){
			WST.msg(json.msg,{icon:2});
			$('#privilegeCode').val('');
		}
	});
}