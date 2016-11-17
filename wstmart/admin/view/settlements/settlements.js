var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/settlements/pageShopQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '<input type="checkbox" onclick="WST.checkChks(this,\'.chk_1\')"/>', width:30,name: 'orderNo',isSort: false,render: function (rowdata, rowindex, value){
            	return '<input type="checkbox" id="s_'+rowdata['shopId']+'" class="chk_1" value="'+rowdata['shopId']+'" dataval="'+rowdata['shopName']+'"/>';
            }},
            { display: '店铺编号', name: 'shopSn',isSort: false},
	        { display: '店铺名称', name: 'shopName',isSort: false},
	        { display: '店主姓名', name: 'shopkeeper',isSort: false},
	        { display: '店主联系电话', name: 'telephone',isSort: false},
	        { display: '待结算订单数', name: 'noSettledOrderNum',isSort: false},
	        { display: '待结算佣金', name: 'noSettledOrderFee',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "<a href='javascript:toView(" + rowdata['shopId'] + ")'>订单列表</a>&nbsp;&nbsp;";
	            return h;
	        }}
        ]
    });
}
function toView(id){
   location.href=WST.U('admin/settlements/toOrders','id='+id);
}
function initOrderGrid(id){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/settlements/pageShopOrderQuery','id='+id),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单号', name: 'orderNo',isSort: false},
	        { display: '支付方式', name: 'payTypeName',isSort: false},
	        { display: '商品金额', name: 'goodsMoney',isSort: false},
	        { display: '运费', name: 'deliverMoney',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '订单总金额', name: 'totalMoney',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '实付金额', name: 'realTotalMoney',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '佣金', name: 'commissionFee',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '下单时间', name: 'createTime',isSort: false}
        ]
    });
}
function loadShopGrid(){
	var areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	grid.set('url',WST.U('admin/settlements/pageShopQuery','shopName='+$('#shopName').val()+"&areaIdPath="+areaIdPath));
}
function loadOrderGrid(){
	var id = $('#id').val();
    grid.set('url',WST.U('admin/settlements/pageShopOrderQuery','orderNo='+$('#orderNo').val()+"&payType="+$('#payType').val()+'&id='+id));
}
var generateNo = 0;
var shops = [];
function generateSettle(){
	var shopId = shops[generateNo];
	var shopName = $('#s_'+shopId).attr('dataval');

	var load = WST.msg('正在生成【'+shopName+'】结算单，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/settlements/generateSettleByShop'),{id:shopId},function(data,textStatus){
		layer.close(load);
		var json = WST.toAdminJson(data);
			if(json.status==1){
				if(generateNo<(shops.length-1)){
					generateNo++;
		            generateSettle();
				}else{
                    WST.msg(json.msg);
                    loadShopGrid();
				}
		}else{
			WST.msg(json.msg);
			loadShopGrid();
		}
	});
}
function generateSettleByShop(){
	var ids = WST.getChks('.chk_1');
	if(ids.length==0){
		WST.msg('请选择要结算的商家!',{icon:2});
		return;
	}
	shops = ids;
	WST.confirm({content:'您确定生成选中商家的结算单吗？',yes:function(){
        generateNo = 0;
	    generateSettle();
	}});
}