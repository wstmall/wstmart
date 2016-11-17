var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/Users/pageQuery'),
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
	        { display: '积分', name: 'userScore', isSort: false},
	        { display: '等级', name: 'rebate', isSort: false},
	        { display: '注册时间', name: 'createTime', isSort: false},
	        { display: '状态', name: 'userStatus', isSort: false, render:function(rowdata, rowindex, value){
	        	return (value==1)?'启用':'停用';
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(WST.GRANT.HYGL_02)h += "<a href='"+WST.U('admin/Users/toEdit','id='+rowdata['userId'])+"'>修改</a> ";
	            if(WST.GRANT.HYGL_03)h += "<a href='javascript:toDel(" + rowdata['userId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });
	
	
	
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/Users/del'),{id:id},function(data,textStatus){
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

function userQuery(){
				var query = WST.getParams('.query');
			    grid.set('url',WST.U('admin/Users/pageQuery',query));
			}



function editInit(){
	 /* 表单验证 */
    $('#userForm').validator({
            dataFilter: function(data) {
                if (data.ok === '该登录账号可用' ) return "";
                else return "已被注册";
            },
            rules: {
                loginName: function(element) {
                    return /\w{5,}/.test(element.value) || '账号应为5-16字母、数字或下划线';
                },
                myRemote: function(element){
                    return $.post(WST.U('admin/users/checkLoginKey'),{'loginName':element.value,'userId':$('#userId').val()},function(data,textStatus){});
                }
            },
            fields: {
                loginName: {
                  rule:"required;loginName;myRemote",
                  msg:{required:"请输入会员账号"},
                  tip:"请输入会员账号",
                  ok:"",
                },
                
                userPhone: {
                  rule:"required;mobile;myRemote",
                  msg:{required:"请输入手机号"},
                  tip:"请输入手机号",
                  ok:"",
                },
                userEmail: {
                  rule:"required;email;myRemote",
                  msg:{required:"请输入邮箱"},
                  tip:"请输入邮箱",
                  ok:"",
                },
                userScore: {
                  rule:"integer[+0]",
                  msg:{integer:"当前积分只能是正整数"},
                  tip:"当前积分只能是正整数",
                  ok:"",
                },
                userTotalScore: {
                  rule:"match[gt, userScore];integer[+0];",
                  msg:{integer:"当前积分只能是正整数",match:'会员历史积分必须大于会员积分'},
                  tip:"当前积分只能是正整数",
                  ok:"",
                },
                userQQ: {
                  rule:"integer[+]",
                  msg:{integer:"QQ只能是数字"},
                  tip:"QQ只能是数字",
                  ok:"",
                },
                
            },

          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/Users/'+((params.userId==0)?"add":"edit")),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toAdminJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
                  location.href=WST.U('Admin/Users/index');
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });

      }

    });



//上传头像
  WST.upload({
      pick:'#adFilePicker',
      formData: {dir:'users'},
      accept: {extensions: 'gif,jpg,jpeg,bmp,png',mimeTypes: 'image/*'},
      callback:function(f){
        var json = WST.toAdminJson(f);
        if(json.status==1){
        $('#uploadMsg').empty().hide();
        //将上传的图片路径赋给全局变量
        $('#userPhoto').val(json.savePath+json.thumb);
        $('#preview').html('<img src="'+WST.conf.ROOT+'/'+json.savePath+json.thumb+'"  height="152" />');
        }else{
          WST.msg(json.msg,{icon:2});
        }
    },
    progress:function(rate){
        $('#uploadMsg').show().html('已上传'+rate+"%");
    }
    });
}