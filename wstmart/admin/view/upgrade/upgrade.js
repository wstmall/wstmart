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
          { display: '版本号', name: 'adClickNum', isSort: false},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	        	var h = "";
	            h += "<a href='"+WST.U('admin/Upgrade/toUpdate','id='+rowdata['adId'])+"'>在线升级</a> ";
	            h += "<a href='javascript:toDel(" + rowdata['adId'] + ")'>手动下载</a> "; 
	            return h;
	        }}
        ]
    });
}

var fl,
    fArr = [],
    loading;

function update(){
  var box = WST.confirm({content:"请备份好文件及数据,以免造成不必要的损失.",yes:function(){
                 loading = WST.msg('正在更新文件，请稍后...', {icon: 16,time:60000});
                 layer.close(box);
                  fl = $('input[id^=filePath]').length;
                  for(var i=1;i<=fl;++i){
                    fArr.push($('#filePath-'+i).val());
                  }
                    realUpdate(1);
              }});
}

function realUpdate(k){
  var v = $('#version').val();
  $('#file-'+k).show();
  $.post(WST.U('admin/upgrade/update'),{filePath:fArr[k-1],version:v},function(data,textStatus){
                    var json = WST.toAdminJson(data);
                    if(json.status=='1'){
                        if(k<fl){
                          $('#file-'+k).attr('src',WST.conf.ROOT+'/Install/images/ok.gif');
                          k++;
                          realUpdate(k);
                        }else{
                          //location.href='index.php?step=3';
                          layer.close(loading);
                          $('#file-'+k).attr('src',WST.conf.ROOT+'/Install/images/ok.gif');
                          updateSuccess();
                        }
                        
                    }else{
                        $('#file-'+k).attr('src',WST.conf.ROOT+'/Install/images/unkown.gif');
                        WST.msg(json.msg,{icon:2});
                    }
                });

}
function updateSuccess(){
  var version = $('#version').val();
  var loading1 = WST.msg('正在更新数据库，请稍后...', {icon: 16,time:60000});
  $.post(WST.U('admin/upgrade/updateVersion'),{version:version},function(data,textStatus){
      layer.close(loading);
      var json = WST.toAdminJson(data);
      if(data.status==1){
        WST.msg(json.msg,{icon:1});
      }else{
        WST.msg(json.msg,{icon:2});
      }

  });
}