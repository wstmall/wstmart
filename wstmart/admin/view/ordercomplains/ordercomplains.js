var grid;
$(function(){
h = WST.pageHeight();
$('.l-tab-content').height(h-25);
$('.l-tab-content-item').height(h-25);
$('.l-tab-content-item').css('overflow-y','auto');
});
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/ordercomplains/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '投诉人', name: 'userName',isSort: false,render:function(rowdata, rowindex, value){
	        	return WST.blank(rowdata['userName'],rowdata['loginName']);
	        }},
	        { display: '投诉订单号', name: 'orderNo',isSort: false},
	        { display: '被投诉人', name: 'shopName',isSort: false},
	        { display: '投诉类型', name: 'complainType',isSort: false,render:function(rowdata, rowindex, value){
	        	var html='';
	        	if(value==1)
	        		html = '承诺的没有做到';
	        	else if(value==2)
	        		html = '未按约定时间发货';
	        	else if(value==3)
	        		html = '未按成交价格进行交易';
	        	else if(value==4)
	        		html = '恶意骚扰';
	        	return html;
	        }},
	        { display: '投诉时间', name: 'complainTime',isSort: false},
	        { display: '状态', name: 'complainStatus',isSort: false,render:function(rowdata, rowindex, value){
	        	var html='';
	        	if(value==0)
	        		html = '新投诉';
	        	else if(value==1)
	        		html = '转给应诉人';
	        	else if(value==2)
	        		html = '应诉人回应';
	        	else if(value==3)
	        		html = '等待仲裁';
	        	else if(value==4)
	        		html = '已仲裁';
	        	return html;
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:toView(" + rowdata['complainId'] + ")'>查看</a> ";
	            if(rowdata['complainStatus']!=4)
	            h += "<a href='javascript:toHandle(" + rowdata['complainId'] + ")'>处理</a> ";
	            return h;
	        }}
            
        ]
    });
}
function toView(id){
	location.href=WST.U('admin/orderComplains/view','cid='+id);
}
function toHandle(id){
	location.href=WST.U('admin/orderComplains/toHandle','cid='+id);
}
function loadGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orderComplains/pageQuery',p.join('&')));
}


function deliverNext(id){
     WST.confirm({content:'您确定要转交给应诉人应诉吗?',yes:function(){
       $.post(WST.U('Admin/Ordercomplains/deliverRespond'),{id:id},function(data,textStatus){
          var json = WST.toAdminJson(data);
          if(json.status=='1'){
        	  WST.msg('投诉已移交应诉人',{icon:1},function(){
        		  location.reload();
        	  });
          }else{
            WST.msg(json.msg,{icon:2});
          }
        });
     }});
}

function finalHandle(id){
   var params = {};
   params.cid = id;
   params.finalResult = $.trim($('#finalResult').val());
   if(params.finalResult==''){
     WST.msg('请输入仲裁结果!',{icon:2});
     return;
   }

   var c = WST.confirm({title:'信息提示',content:'您确定仲裁该订单投诉吗?',yes:function(){
     layer.close(c);
     $.post(WST.U('Admin/OrderComplains/finalHandle'),params,function(data,textStatus){
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
          WST.msg(json.msg,{icon:1});
          location.reload();
        }else{
          WST.msg(json.msg,{icon:2});
        }
      });
   }});
}

  
