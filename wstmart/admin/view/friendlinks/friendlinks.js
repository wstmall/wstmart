var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/Friendlinks/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '网站名称', name: 'friendlinkName', isSort: false},
	        { display: '网址', name: 'friendlinkUrl', isSort: false},
	        { display: '图标', name: 'friendlinkIco', isSort: false,render:function(rowdata, rowindex, value){
	        	return '<img src="'+WST.conf.ROOT+'/'+rowdata['friendlinkIco']+'" height="28px" />';
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(WST.GRANT.YQGL_02)h += "<a href='javascript:getForEdit(" + rowdata['friendlinkId'] + ")'>修改</a> ";
	            if(WST.GRANT.YQGL_03)h += "<a href='javascript:toDel(" + rowdata['friendlinkId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });

}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/friendlinks/del'),{id:id},function(data,textStatus){
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

function getForEdit(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
     $.post(WST.U('admin/friendlinks/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.friendlinkId){
           		WST.setValues(json);
           		//显示原来的图片
           		if(json.friendlinkIco!=''){
           			$('#preview').html('<img src="'+WST.conf.ROOT+'/'+json.friendlinkIco+'" height="70px" />');
           		}else{
           			$('#preview').empty();
           		}
           		toEdit(json.friendlinkId);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
	var title =(id==0)?"新增":"编辑";
	var box = WST.open({title:title,type:1,content:$('#friendlinkBox'),area: ['450px', '350px'],btn: ['确定','取消'],yes:function(){
			$('#friendlinkForm').submit();
	},cancel:function(){
		//重置表单
		$('#friendlinkForm')[0].reset();
		//清空预览图
		$('#preview').html('');
		//清空图片隐藏域
		$('#friendlinkIco').val('');
	},end:function(){
		//重置表单
		$('#friendlinkForm')[0].reset();
		//清空预览图
		$('#preview').html('');
		//清空图片隐藏域
		$('#friendlinkIco').val('');

	}});
	$('#friendlinkForm').validator({
        fields: {
            friendlinkName: {
            	rule:"required;",
            	msg:{required:"网站名称不能为空"},
            	tip:"请输入网站名称",
            	ok:"",
            },
            friendlinkUrl: {
	            rule: "required;",
	            msg: {required: "网址不能为空"},
	            tip: "请输入网址",
	            ok: "",
        	}
        },
       valid: function(form){
		        var params = WST.getParams('.ipt');
		        params.friendlinkId = id;
		        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		   		$.post(WST.U('admin/friendlinks/'+((id==0)?"add":"edit")),params,function(data,textStatus){
		   			  layer.close(loading);
		   			  var json = WST.toAdminJson(data);
		   			  if(json.status=='1'){
		   			    	WST.msg("操作成功",{icon:1});
		   			    	$('#friendlinkForm')[0].reset();
		   			    	//清空预览图
		   			    	$('#preview').html('');
		   			    	//清空图片隐藏域
		   			    	$('#friendlinkIco').val('');
		   			    	layer.close(box);
		   		            grid.reload();
		   			  }else{
		   			        WST.msg(json.msg,{icon:2});
		   			  }
		   		});

    	}

  });
}



$(function(){
//文件上传
WST.upload({
    pick:'#adFilePicker',
    formData: {dir:'friendlinks'},
    accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
    callback:function(f){
      var json = WST.toAdminJson(f);
      if(json.status==1){
	      $('#uploadMsg').empty().hide();
	   	  $('#friendlinkIco').val(json.savePath+json.thumb);
	   	  $('#preview').html('<img src="'+WST.conf.ROOT+'/'+json.savePath+json.thumb+'" height="75" />');
      }else{
      	  WST.msg(json.msg,{icon:2});
      }
  },
  progress:function(rate){
      $('#uploadMsg').show().html('已上传'+rate+"%");
  }
});

});
