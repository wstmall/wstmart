var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/Adpositions/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '位置名称', name: 'positionName', isSort: false ,width:'50%',heightAlign:'left',align:'left'},
	        { display: '宽度', name: 'positionWidth', isSort: false},
	        { display: '高度', name: 'positionHeight', isSort: false},
	        { display: '位置类型', name: 'positionType', isSort: false,render:function(rowdata, rowindex, value){
	        	return (rowdata['positionType']==1)?'PC版':'微信版';
	        }},
          { display: '位置代码', name: 'positionCode', isSort: false},
	        { display: '排序号', name: 'apSort', isSort: false},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	        	var h = "";
	            if(WST.GRANT.GGWZ_02)h += "<a href='"+WST.U('admin/AdPositions/toEdit','id='+rowdata['positionId'])+"'>修改</a> ";
	            if(WST.GRANT.GGWZ_03)h += "<a href='javascript:toDel(" + rowdata['positionId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/AdPositions/del'),{id:id},function(data,textStatus){
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



function editInit(){
	 /* 表单验证 */
    $('#adPositionsForm').validator({
            fields: {
                positionType: {
                  rule:"required",
                  msg:{required:"请选择位置类型"},
                  tip:"请选择位置类型",
                  ok:"",
                },
                positionName: {
                  rule:"required;",
                  msg:{required:"请输入位置名称"},
                  tip:"请输入位置名称",
                  ok:"",
                },
                positionCode: {
                    rule:"required;",
                    msg:{required:"请输入位置代码"},
                    tip:"请输入位置代码",
                    ok:"",
                  },
                positionWidth: {
                  rule:"required;",
                  msg:{required:"请输入建议宽度"},
                  ok:"",
                },
                positionHeight: {
                  rule:"required",
                  msg:{required:"请输入建议高度"},
                  ok:"",
                }
            },
          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/Adpositions/'+((params.positionId==0)?"add":"edit")),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toAdminJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
                  location.href=WST.U('Admin/Adpositions/index');
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });
      }
    });
}