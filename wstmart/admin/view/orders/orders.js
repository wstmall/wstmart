var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/orders/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单编号', name: 'orderNo',isSort: false},
	        { display: '收货人', name: 'userName',isSort: false},
	        { display: '店铺', name: 'shopName',isSort: false},
	        { display: '订单总金额', name: 'totalMoney',isSort: false},
	        { display: '实收金额', name: 'realTotalMoney',isSort: false},
	        { display: '支付方式', name: 'payType',isSort: false},
	        { display: '配送方式', name: 'deliverType',isSort: false},
	        { display: '下单时间', name: 'createTime',isSort: false},
	        { display: '订单状态', name: 'status'},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:toView(" + rowdata['orderId'] + ")'>详情</a> ";
	            return h;
	        }}
        ]
    });
}

function toView(id){
	location.href=WST.U('admin/orders/view','id='+id);
}
function loadGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orders/pageQuery',p.join('&')));
}
function toExport(){
	var params = {};
	params = WST.getParams('.j-ipt');
	var box = WST.confirm({content:"您确定要导出订单吗?",yes:function(){
		layer.close(box);
		location.href=WST.U('admin/orders/toExport',params);
         }});
}