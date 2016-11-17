$(function(){
  queryByList();
});
function queryByList(p){
     var params = {};
     params.page = p;
     var load = WST.load({msg:'正在加载信息，请稍后...'})
     $.post(WST.U('Home/Messages/pageQuery'),params,function(data,textStatus){
    	 layer.close(load);
        var json = WST.toJson(data);
	    if(json.data){
		  json = json.data;
	      var gettpl = document.getElementById('msg').innerHTML;
	      //复选框为未选中状态
	      $('#all').attr('checked',false);
	      laytpl(gettpl).render(json.Rows, function(html){
	          $('#msg_box').html(html);
	      });
	      if(json.TotalPage>1){
            laypage({
               cont: 'wst-page', 
               pages:json.TotalPage, 
               curr: json.CurrentPage,
               skin: '#e23e3d',
               groups: 3,
               jump: function(e, first){
                    if(!first){
                      queryByList(e.curr);
                    }
                  } 
            });
          }else{
            $('#wst-page').empty();
          }
	  }
  });
}

function showMsg(id){
  location.href=WST.U('home/messages/showMsg','msgId='+id);
}

function delMsg(obj,id){
WST.confirm({content:"您确定要删除该消息吗？", yes:function(tips){
  var ll = WST.load('数据处理中，请稍候...');
  $.post(WST.U('Home/messages/del'),{id:id},function(data,textStatus){
    layer.close(ll);
      layer.close(tips);
    var json = WST.toJson(data);
    if(json.status=='1'){
      WST.msg('操作成功!', {icon: 1}, function(){
         queryByList();
      });
    }else{
      WST.msg('操作失败!', {icon: 5});
    }
  });
}});
}
function batchDel(){
    var ids = WST.getChks('.chk');
    if(ids==''){
      WST.msg('请选择要删除的消息!', {icon: 5});
      return;
    }
    WST.confirm({content:"您确定要删除该消息吗？", yes:function(tips){
        var params = {};
        params.ids = ids;
        var load = WST.load({msg:'请稍后...'});
        $.post(WST.U('home/messages/batchDel'),params,function(data,textStatus){
          layer.close(load);
          var json = WST.toJson(data);
          if(json.status=='1'){
            WST.msg('操作成功',{icon:1},function(){
                 queryByList();
            });
          }else{
            WST.msg('操作失败',{icon:5});
          }
        });
    }});
}
function batchRead(){
    var ids = WST.getChks('.chk');
    if(ids==''){
      WST.msg('请选择处理的消息!', {icon: 5});
      return;
    }
    WST.confirm({content:"您确定要将这些消息标记为已读吗？", yes:function(tips){
        var params = {};
        params.ids = ids;
        var load = WST.load({msg:'请稍后...'});
        $.post(WST.U('home/messages/batchRead'),params,function(data,textStatus){
          layer.close(load);
          var json = WST.toJson(data);
          if(json.status=='1'){
            WST.msg('操作成功',{icon:1},function(){
                 queryByList();
            });
          }else{
            WST.msg('操作失败',{icon:5});
          }
        });
    }});
}
