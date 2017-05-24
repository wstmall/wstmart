var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/userranks/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '会员等级图标', name: 'userrankImg', isSort: false,render:function(rowdata, rowindex, value){
	        	return '<img src="'+WST.conf.ROOT+'/'+rowdata['userrankImg']+'" height="28px" />';
	        }},
	        { display: '会员等级名称', name: 'rankName', isSort: false},
	        { display: '积分下限', name: 'startScore', isSort: false},
	        { display: '积分上限', name: 'endScore', isSort: false},
	        { display: '折扣率(%)', name: 'rebate', isSort: false},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(WST.GRANT.HYDJ_02)h += "<a href='"+WST.U('admin/userranks/toEdit','id='+rowdata['rankId'])+"'>修改</a> ";
	            if(WST.GRANT.HYDJ_03)h += "<a href='javascript:toDel(" + rowdata['rankId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/userranks/del'),{id:id},function(data,textStatus){
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
    $('#userRankForm').validator({
            fields: {
                rankName: {
                  rule:"required",
                  msg:{required:"请输入会员等级名称"},
                  tip:"请输入会员等级名称",
                  ok:"",
                },
                userrankImg: {
                  rule:"required",
                  msg:{required:"请输上传会员图标"},
                  tip:"请输上传会员图标",
                  ok:"",
                }
                
            },

          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/userranks/'+((params.rankId==0)?"add":"edit")),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toAdminJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
                  location.href=WST.U('Admin/userranks/index');
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });

      }

    });

//文件上传
WST.upload({
    pick:'#userranksPicker',
    formData: {dir:'userranks'},
    accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
    callback:function(f){
      var json = WST.toAdminJson(f);
      if(json.status==1){
      $('#uploadMsg').empty().hide();
      //保存上传的图片路径
      $('#userrankImg').val(json.savePath+json.thumb);
      $('#preview').html('<img src="'+WST.conf.ROOT+'/'+json.savePath+json.thumb+'" height="25" />');
      }else{
        WST.msg(json.msg,{icon:2});
      }
  },
  progress:function(rate){
      $('#uploadMsg').show().html('已上传'+rate+"%");
  }
});


};
  




		