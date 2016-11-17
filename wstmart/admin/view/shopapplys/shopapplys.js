var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/shopapplys/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '申请人', name: 'userName',isSort: false,render: function (rowdata, rowindex, value){
	            return rowdata['linkman']+WST.blank(rowdata['loginName']);
	        }},
	        { display: '联系电话', name: 'phoneNo',Sort: false},
	        { display: '申请说明', name: 'applyDesc',Sort: false},
	        { display: '申请时间', name: 'createTime',Sort: false},
	        { display: '状态', name: 'applyStatus',Sort: false,render: function (rowdata, rowindex, value){
	            return (rowdata['applyStatus']==1)?"已处理":((rowdata['applyStatus']==-1)?"申请失败":"未处理");
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(rowdata['applyStatus']==0 && WST.GRANT.DPSQ_04)h += "<a href='javascript:toEdit(" + rowdata['applyId'] + ")'>处理</a> ";
	            if(WST.GRANT.DPSQ_03)h += "<a href='javascript:toDel(" + rowdata['applyId'] + ")'>删除</a> ";
	            if(WST.GRANT.DPGL_01 && !rowdata['shopId'] && rowdata['applyStatus']==1)h += "<a href='javascript:toAddShop(" + rowdata['applyId'] + ")'>开店</a> ";
	            return h;
	        }}
        ]
    });
}
function toEdit(id){
	location.href=WST.U('admin/shopapplys/toHandle','id='+id);
}
function toAddShop(id){
	location.href=WST.U('admin/shops/toAddByApply','id='+id);
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该开店申请吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           $.post(WST.U('admin/shopapplys/del'),{id:id},function(data,textStatus){
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
function save(){
	if(!$('input[name="applyStatus"]').isValid())return;
	if($('input[name="applyStatus"]:checked').val()==-1 && !$('#handleDesc').isValid())return;
	var params = WST.getParams('.ipt');
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/shopapplys/handle'),params,function(data,textStatus){
    	layer.close(loading);
    	var json = WST.toAdminJson(data);
    	if(json.status=='1'){
    		WST.msg("操作成功",{icon:1});
    		if(WST.GRANT.DPGL_01 && params.applyStatus==1){
    			toAddShop(params.applyId);
    		}else{
    		    location.href=WST.U('admin/shopapplys/index');
    		}
    	}else{
    		WST.msg(json.msg,{icon:2});
    	}
    });
}
