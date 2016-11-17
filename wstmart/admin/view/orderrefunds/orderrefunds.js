var grid;
function toView(id){
	location.href=WST.U('admin/orders/view','id='+id);
}
function initRefundGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/orderrefunds/refundPageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单编号', name: 'orderNo',isSort: false},
	        { display: '申请人', name: 'loginName',isSort: false},
	        { display: '店铺', name: 'shopName',isSort: false},
	        { display: '配送方式', name: 'deliverType',isSort: false},
	        { display: '实收金额', name: 'realTotalMoney',isSort: false,render: function (rowdata, rowindex, value){
	        	return "¥"+value;
	        }},
	        { display: '申请退款金额', name: 'backMoney',isSort: false,render: function (rowdata, rowindex, value){
	        	return "¥"+value;
	        }},
	        { display: '申请时间', name: 'createTime',isSort: false},
	        { display: '退款状态', name: 'isRefund',render: function (rowdata, rowindex, value){
	        	return (rowdata['isRefund']==1)?"已退款":"未退款";
	        }},
	        { display: '退款备注', name: 'refundRemark'},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	        	var h = '';
	            if(rowdata['isRefund']==0){
	            	if(WST.GRANT.TKDD_04)h += "<a href='javascript:toRefund(" + rowdata['refundId'] + ")'>退款</a> ";
	            }
	            h += "<a href='javascript:toView(" + rowdata['orderId'] + ")'>详情</a> ";
	            return h;
	        }}
        ]
    });
}
function loadRefundGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orderrefunds/refundPageQuery',p.join('&')));
}
var w;
function toRefund(id){
	var ll = WST.msg('正在加载信息，请稍候...');
	$.post(WST.U('admin/orderrefunds/toRefund',{id:id}),{},function(data){
		layer.close(ll);
		w =WST.open({type: 1,title:"订单退款",shade: [0.6, '#000'],offset:'50px',border: [0],content:data,area: ['550px', '370px']});
	});
}
function orderRefund(id){
	$('#editFrom').isValid(function(v){
		if(v){
        	var params = {};
        	params.content = $.trim($('#content').val());
        	params.id = id;
        	ll = WST.msg('正在加载信息，请稍候...');
		    $.post(WST.U('admin/orderrefunds/orderRefund'),params,function(data){
		    	layer.close(ll);
		    	var json = WST.toAdminJson(data);
				if(json.status==1){
					WST.msg(json.msg, {icon: 1});
					loadRefundGrid();
					layer.close(w);
				}else{
					WST.msg(json.msg, {icon: 2});
				}
		   });
		}
    })
}