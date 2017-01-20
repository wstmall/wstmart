$(function (){ 
var h = WST.pageHeight();
$('.l-tab-content').height(h-32);
$('.l-tab-content-item').height(h-32);
$('.l-tab-content-item').css('overflow-y','auto');
$("#wst-tabs").ligerTab({
    height: '99%',
      changeHeightOnResize:true,
      showSwitchInTab : false,
      showSwitch: false,
      onAfterSelectTabItem:function(n){
         listQuery(n)
    }
});
listQuery('home');
});
function listQuery(styleSys){
	var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/styles/listQueryBySys'),{styleSys:styleSys},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
			var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$('#style_'+styleSys).html(html);
	       	});
	       	$('.btn').click(function(){
                changeStyle($(this),$(this).attr('dataid'));
            });
		}
	});
}
function changeStyle(obj,id){
	if(obj.hasClass('btn-disabled'))return;
	var box = WST.confirm({content:"您确定要使用这套风格吗?",yes:function(){
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/styles/changeStyle'),{id:id},function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json.status=='1'){
				WST.msg(json.msg,{icon:1});
				layer.close(box);
				$('.btn-disabled').attr('disabled',false).val('启用').removeClass('btn-disabled').addClass('btn-blue');
				$('.style_'+id).removeClass('btn-blue').addClass('btn-disabled').attr('disabled',true).val('应用中');
			}else{
				WST.msg(json.msg,{icon:2});
			}
		});
	}});
}