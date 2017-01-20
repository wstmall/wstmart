function userAddrEditInit(){
 /* 表单验证 */
    $('#useraddressForm').validator({
          fields: {
                userAddress: {
                  rule:"required;length[~60, true]",
                  msg:{required:"请输入您的收货地址"},
                  tip:"请输入您的收货地址",
                  ok:"",
                },
                userName: {
                  rule:"required;length[~12, true]",
                  msg:{required:"请输入您的名称"},
                  tip:"请输入您的名称",
                  ok:"",
                },
                userPhone: {
                  rule:"required;length[~50, true]",
                  msg:{required:"联系电话"},
                  tip:"请输入您的联系电话",
                  ok:"",
                },
                isDefault: {
                    rule:"checked;",
                    msg:{checked:"至少选择一项"},
                    tip:"是否作为默认地址",
                    ok:"",
                }
          },
          valid: function(form){
        	var isNoSelected = false;
        	$('.j-areas').each(function(){
        		isSelected = true;
        		if($(this).val()==''){
        		    isNoSelected = true;
        			return;
        		}
        	});
        	if(isNoSelected){
        		WST.msg('请选择完整区域！',{icon:2});
        		return;
        	}
            var params = WST.getParams('.ipt');
            params.areaId = WST.ITGetAreaVal('j-areas');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('home/useraddress/'+((params.addressId==0)?"add":"toEdit")),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toJson(data);
              if(json.status=='1'){
                  WST.msg(json.msg,{icon:1});
                  location.href=WST.U('home/useraddress/index');
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });

      }

    });
 }

function getCommunity(areaId3, val){
    $.post(WST.U('home/Communitys/getCommunity'),{'areaId3':areaId3},function(data,textStatus){
        var json = WST.toJson(data);
        var opts,html=[];
        html.push("<option value=''>请选择</option>");
        $(data).each(function(k,v){
          html.push('<option value="'+v.communityId+'" '+((val==v.communityId)?'selected':'')+'>'+v.communityName+'</option>');
        });
        $("#communityId").html(html);
    });
}
function listQuery(){
   $.post(WST.U('Home/Useraddress/listQuery'),'',function(data,textStatus){
    var json = WST.toJson(data);
    if(json.status==1 && json.data){
      json = json.data;
	    var count = json.length;//已添加的记录数
	    $('.g1').html(count);
	    var gettpl = document.getElementById('address').innerHTML;
	    laytpl(gettpl).render(json, function(html){
	        $('#address_box').html(html);
	    });
    }else{
    	$('#address_box').empty();
    }
});
}

function editAddress(id){
   location.href=WST.U('home/useraddress/edit','id='+id);
}

function delAddress(id,t){
  WST.confirm({content:"您确定要删除该地址吗？",yes:function(tips){
    var ll = layer.load('数据处理中，请稍候...');
    $.post(WST.U('Home/UserAddress/del'),{id:id},function(data,textStatus){
      layer.close(ll);
        layer.close(tips);
      var json = WST.toJson(data);
      if(json.status=='1'){
        WST.msg('操作成功!', {icon: 1}, function(){
        	listQuery();
        });
      }else{
        WST.msg('操作失败!', {icon: 5});
      }
    });
  }});

}
function setDefault(id){
   WST.confirm({content:"您确定设置为默认地址吗？",yes:function(tips){
    var ll = layer.load('数据处理中，请稍候...');
    $.post(WST.U('Home/UserAddress/setDefault'),{id:id},function(data,textStatus){
      layer.close(ll);
        layer.close(tips);
      var json = WST.toJson(data);
      if(json.status=='1'){
        WST.msg('操作成功!', {icon: 1}, function(){
        	listQuery();
        });
      }else{
        WST.msg('操作失败!', {icon: 5});
      }
    });
  }});
}
