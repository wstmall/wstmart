var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/Ads/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '标题', name: 'adName', isSort: false},
	        { display: '广告位置', name: 'adPositionId', isSort: false,render:function(rowdata, rowindex, value){
	        	return rowdata['positionName'];
	        }},
	        { display: '广告网址', name: 'adURL', isSort: false},
	        { display: '广告开始日期', name: 'adStartDate', isSort: false},
	        { display: '广告结束日期', name: 'adEndDate', isSort: false},
	        { display: '图标', name: 'adFile', height: '300', isSort: false,render:function(rowdata, rowindex, value){
            var adFile = rowdata['adFile'].split(',');
              return'<img src="'+WST.conf.ROOT+'/'+adFile[0]+'" height="28px" />';
	        }},
          { display: '点击数', name: 'adClickNum', isSort: false},
	        { display: '排序号', name: 'adSort', isSort: false,render:function(rowdata, rowindex, value){
              return '<span style="cursor:pointer;" ondblclick="changeSort(this,'+rowdata["adId"]+');">'+value+'</span>';
          }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	        	var h = "";
	            if(WST.GRANT.GGGL_02)h += "<a href='"+WST.U('admin/Ads/toEdit','id='+rowdata['adId'])+"'>修改</a> ";
	            if(WST.GRANT.GGGL_03)h += "<a href='javascript:toDel(" + rowdata['adId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/Ads/del'),{id:id},function(data,textStatus){
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

var oldSort;
function changeSort(t,id){
 $(t).attr('ondblclick'," ");
var html = "<input type='text' id='sort-"+id+"' style='width:30px;' onblur='doneChange(this,"+id+")' value='"+$(t).html()+"' />";
 $(t).html(html);
 $('#sort-'+id).focus();
 $('#sort-'+id).select();
 oldSort = $(t).html();
}
function doneChange(t,id){
  var sort = ($(t).val()=='')?0:$(t).val();
  if(sort==oldSort){
    $(t).parent().attr('ondblclick','changeSort(this,'+id+')');
    $(t).parent().html(parseInt(sort));
    return;
  }
  $.post(WST.U('admin/ads/changeSort'),{id:id,adSort:sort},function(data){
    var json = WST.toAdminJson(data);
    if(json.status==1){
        $(t).parent().attr('ondblclick','changeSort(this,'+id+')');
        $(t).parent().html(parseInt(sort));
    }
  });
}


		
//查询
function adsQuery(){
		var query = WST.getParams('.query');
	    grid.set('url',WST.U('admin/ads/pageQuery',query));
}

function editInit(){
  //文件上传
	WST.upload({
  	  pick:'#adFilePicker',
  	  formData: {dir:'adspic'},
      compress:false,//默认不对图片进行压缩
  	  accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
  	  callback:function(f){
  		  var json = WST.toAdminJson(f);
  		  if(json.status==1){
  			$('#uploadMsg').empty().hide();
        var html = '<img src="'+WST.conf.ROOT+'/'+json.savePath+json.thumb+'" />';
        $('#preview').html(html);
        // 图片路径
        $('#adFile').val(json.savePath+json.thumb);
  		  }
	  },
	  progress:function(rate){
	      $('#uploadMsg').show().html('已上传'+rate+"%");
	  }
    });
  

 /* 表单验证 */
    $('#adsForm').validator({
            fields: {
                adPositionId: {
                  rule:"required",
                  msg:{required:"请选择广告位置"},
                  tip:"请选择广告位置",
                  ok:"验证通过",
                },
                adName: {
                  rule:"required;",
                  msg:{required:"广告标题不能为空"},
                  tip:"请输入广告标题",
                  ok:"验证通过",
                },
                adFile: {
                  rule:"required;",
                  msg:{required:"请上传广告图片"},
                  tip:"请上传广告图片",
                  ok:"",
                },
                adStartDate: {
                  rule:"required;match(lt, adEndDate, date)",
                  msg:{required:"请选择广告开始时间",match:"必须小于广告结束时间"},
                  ok:"验证通过",
                },
                adEndDate: {
                  rule:"required;match(gt, adStartDate, date)",
                  msg:{required:"请选择广告结束时间",match:"必须大于广告开始时间"},
                  ok:"验证通过",
                }
            },
          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/Ads/'+((params.adId==0)?"add":"edit")),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toAdminJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
                  location.href=WST.U('Admin/Ads/index');
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });
      }
    });
}


var positionInfo;
/*获取地址*/
function addPosition(pType, val, getSize)
{
    $.post(WST.U('admin/Adpositions/getPositon'),{'positionType':pType},function(data,textStatus){
        positionInfo = data;
        var html='<option value="">请选择</option>';
        $(data).each(function(k,v){
			var selected;
            if(v.positionId==val){
              selected = 'selected="selected"';
              getPhotoSize(v.positionId);
            }
            html +='<option '+selected+' value="'+v.positionId+'">'+v.positionName+'</option>';
        });
        $('#adPositionId').html(html);
    })
}
/*获取图片尺寸 以及设置图片显示方式*/
function getPhotoSize(pType)
{
  $(positionInfo).each(function(k,v){
      if(v.positionId==pType){
        $('#img_size').html(v.positionWidth+'x'+v.positionHeight);
        if(v.positionWidth>v.positionHeight){
             $('.ads-h-list').removeClass('ads-h-list').addClass('ads-w-list');
         }
      }
  });

}