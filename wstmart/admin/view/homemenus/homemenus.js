var grid;
$(function(){
  grid = $("#maingrid").WSTGridTree({
    url:WST.U('admin/homemenus/pageQuery'),
    pageSize:10000,
    pageSizeOptions:10000,
    height:'99%',
        width:'100%',
        minColToggle:6,
        delayLoad :true,
        rownumbers:true,
        columns: [
          { display: '菜单名称', name: 'menuName', id:"menuId", isSort: false},
          { display: '菜单类型', name: 'menuType', isSort: false,render:function(rowdata, rowindex, value){
            return (rowdata['menuType']==0)?'用户菜单':'商家菜单';
          }},
          { display: '菜单Url', name: 'menuUrl', isSort: false},
          { display: '是否显示', name: 'isShow', isSort: false,render :function(rowdata, rowindex, value){
            return (rowdata['isShow']==1)?'<span style="cursor:pointer" onclick="isShowtoggle('+rowdata['menuId']+',this)" v="0">显示</span>':'<span style="cursor:pointer" onclick="isShowtoggle('+rowdata['menuId']+', this)" v="1">隐藏</span>';
          }},
          { display: '排序号', name: 'menuSort', isSort: false,render:function(rowdata,rowindex,value){
             return '<span style="cursor:pointer;" ondblclick="changeSort(this,'+rowdata["menuId"]+');">'+rowdata['menuSort']+'</span>';
          }},
          { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
              var h = "";
              if(WST.GRANT.QTCD_01)h += "<a href='javascript:toEdit(0," + rowdata['menuId'] + ","+rowdata['menuType']+")'>添加子菜单</a> ";
              if(WST.GRANT.QTCD_02)h += "<a href='javascript:getForEdit("+rowdata["parentId"]+"," + rowdata['menuId'] + ")' href='"+WST.U('admin/homemenus/toEdit','menuId='+rowdata['menuId'])+"'>修改</a> ";
              if(WST.GRANT.QTCD_03)h += "<a href='javascript:toDel("+rowdata["parentId"]+"," + rowdata['menuId'] + ")'>删除</a> "; 
              return h;
          }}
        ]
    });
})


var oldSort;
function changeSort(t,id){
  if(!WST.GRANT.QTCD_02)return;
  $(t).attr('ondblclick'," ");
var html = "<input type='text' id='sort-"+id+"' style='width:30px;' onblur='doneChange(this,"+id+")' value='"+$(t).html()+"' />";
 $(t).html(html);
 $('#sort-'+id).focus();
 $('#sort-'+id).select();
}
function doneChange(t,id){
  var sort = ($(t).val()=='')?0:$(t).val();
  if(sort==oldSort){
    $(t).parent().attr('ondblclick','changeSort(this,'+id+')');
    $(t).parent().html(parseInt(sort));
    return;
  }
  $.post(WST.U('admin/homemenus/changeSort'),{id:id,menuSort:sort},function(data){
    var json = WST.toAdminJson(data);
    if(json.status==1){
        $(t).parent().attr('ondblclick','changeSort(this,'+id+')');
        $(t).parent().html(parseInt(sort));
    }
  });
}




function toDel(pid,menuId){
  var box = WST.confirm({content:"删除该菜单会将下边的子菜单也一并删除，您确定要删除吗?",yes:function(){
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/homemenus/del'),{menuId:menuId},function(data,textStatus){
      layer.close(loading);
      var json = WST.toAdminJson(data);
      if(json.status=='1'){
        WST.msg("操作成功",{icon:1});
        layer.close(box);
        
        loadGrid();
      }else{
        WST.msg(json.msg,{icon:2});
      }
    });
  }});
}



function edit(pid,menuId){
  //获取所有参数
  var params = WST.getParams('.ipt');
    params.menuId = menuId;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/homemenus/'+((menuId==0)?"add":"edit")),params,function(data,textStatus){
      layer.close(loading);
      var json = WST.toAdminJson(data);
      if(json.status=='1'){
          WST.msg("操作成功",{icon:1});
          loadGrid();
      }else{
            WST.msg(json.msg,{icon:2});
      }
    });
}
function isShowtoggle(menuId, t){
  if(!WST.GRANT.QTCD_02)return;
  var isShow = $(t).attr('v');
  $.post(WST.U('admin/homemenus/setToggle'), {'menuId':menuId, 'isShow':isShow}, function(data, textStatus){
    var json = WST.toAdminJson(data);
    if(json.status=='1'){
      WST.msg("操作成功",{icon:1});
      var v = (isShow==0)?1:0;
      var text = (v==0)?'显示':'隐藏';
      $(t).attr('v',v).html(text);
      loadGrid();
    }else{
      WST.msg(json.msg,{icon:2});
    }
  })
}

function getForEdit(pid,menuId){
  $('#menuForm')[0].reset();
  var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/homemenus/get'),{menuId:menuId},function(data,textStatus){
          layer.close(loading);
          var json = WST.toAdminJson(data);
          if(json.menuId){
              WST.setValues(json);
              toEdit(json.menuId,pid);
          }else{
              WST.msg(json.msg,{icon:2});
          }
   });
}

function toEdit(menuId,parentId,tId){
  var title = "编辑";
  if(menuId==0){
    $('#menuForm')[0].reset();
    title = "新增";
    WST.setValue('isShow',1);
  }
  if(parentId>0){
	  $('#menuTypes').hide();
  }else{
	  $('#menuTypes').show();
  }
  if(tId==1){$('#menuType').val(1);}
  var box = WST.open({title:title,type:1,content:$('#menuBox'),area: ['550px', '350px'],btn:['确定','取消'],yes:function(){
    $('#menuForm').submit();
  }});
  $('#menuForm').validator({
        fields: {
          'menuName': {rule:"required;",msg:{required:'请输入菜单名称'}},
          'menuUrl': {rule:"required;",msg:{required:'请输入菜单Url'}},
          'menuSort': {rule:"required;integer",msg:{required:'请输入排序号',number:"请输入数字"}},
          'isShow': {rule:"checked;",msg:{checked:'请选择是否显示'}},
        },
        valid: function(form){
          var params = WST.getParams('.ipt');
            params.menuId = menuId;
            params.parentId = parentId;
        
          var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
         $.post(WST.U('admin/homemenus/'+((menuId==0)?"add":"edit")),params,function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.status=='1'){
                WST.msg("操作成功",{icon:1});
                $('#menuForm')[0].reset();
                layer.close(box);
                
                loadGrid();
                $('#menuForm')[0].reset();
           }else{
             WST.msg(json.msg,{icon:2});
            }
          });

      }

  });
}
function loadGrid(){
    $("#maingrid").WSTGridTree({
    url:WST.U('admin/homemenus/pageQuery',{menuType:$('#s_menuType').val()}),
    pageSize:10000,
    pageSizeOptions:10000,
    height:'99%',
        width:'100%',
        minColToggle:6,
        delayLoad :true,
        rownumbers:true,
        columns: [
          { display: '菜单名称', name: 'menuName', id:"menuId", isSort: false},
          { display: '菜单类型', name: 'menuType', isSort: false,render:function(rowdata, rowindex, value){
            return (rowdata['menuType']==0)?'用户菜单':'商家菜单';
          }},
          { display: '菜单Url', name: 'menuUrl', isSort: false},
          { display: '是否显示', name: 'isShow', isSort: false,render :function(rowdata, rowindex, value){
            return (rowdata['isShow']==1)?'<span style="cursor:pointer" onclick="isShowtoggle('+rowdata['menuId']+',this)" v="0">显示</span>':'<span style="cursor:pointer" onclick="isShowtoggle('+rowdata['menuId']+', this)" v="1">隐藏</span>';
          }},
          { display: '排序号', name: 'menuSort', isSort: false,render:function(rowdata,rowindex,value){
             return '<span style="cursor:pointer;" ondblclick="changeSort(this,'+rowdata["menuId"]+');">'+rowdata['menuSort']+'</span>';
          }},
          { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
              var h = "";
              if(WST.GRANT.QTCD_01)h += "<a href='javascript:toEdit(0," + rowdata['menuId'] + ","+rowdata['menuType']+")'>添加子菜单</a> ";
              if(WST.GRANT.QTCD_02)h += "<a href='javascript:getForEdit("+rowdata["parentId"]+"," + rowdata['menuId'] + ")' href='"+WST.U('admin/homemenus/toEdit','menuId='+rowdata['menuId'])+"'>修改</a> ";
              if(WST.GRANT.QTCD_03)h += "<a href='javascript:toDel("+rowdata["parentId"]+"," + rowdata['menuId'] + ")'>删除</a> "; 
              return h;
          }}
        ]
    });
}