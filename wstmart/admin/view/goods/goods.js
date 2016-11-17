var grid;
function initSaleGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/goods/saleByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        rowHeight:65,
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '&nbsp;', name: 'goodsName',width:60,align:'left',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
            	return "<img style='height:60px;width:60px;' src='"+WST.conf.ROOT+"/"+rowdata['goodsImg']+"'>";
            }},
	        { display: '商品名称', name: 'goodsName',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
	            return rowdata['goodsName'];
	        }},
	        { display: '商品编号', name: 'goodsSn',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['goodsSn']+"</div>";
	        }},
	        { display: '价格', name: 'shopPrice',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['shopPrice']+"</div>";
	        }},
	        { display: '所属店铺', name: 'shopName',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['shopName']+"</div>";
	        }},
	        { display: '所属分类', name: 'goodsCatName',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['goodsCatName']+"</div>";
	        }},
	        { display: '销量', name: 'saleNum',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['saleNum']+"</div>";
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<div class='goods-valign-m'><a target='_blank' href='"+WST.U("home/goods/detail","id="+rowdata['goodsId'])+"'>查看</a> ";
	            if(WST.GRANT.SJSP_04)h += "<a href='javascript:illegal(" + rowdata['goodsId'] + ")'>违规下架</a> ";
	            if(WST.GRANT.SJSP_03)h += "<a href='javascript:del(" + rowdata['goodsId'] + ",1)'>删除</a></div> "; 
	            return h;
	        }}
        ]
    });
}
function loadSaleGrid(){
	var params = WST.getParams('.j-ipt');
	params.areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	params.goodsCatIdPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats').join('_');
	grid.set('url',WST.U('admin/goods/saleByPage',params));
}

function del(id,type){
	var box = WST.confirm({content:"您确定要删除该商品吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           $.post(WST.U('admin/goods/del'),{id:id},function(data,textStatus){
	           			layer.close(loading);
	           			var json = WST.toAdminJson(data);
	           			if(json.status=='1'){
	           			    WST.msg(json.msg,{icon:1});
	           			    layer.close(box);
	           			    if(type==0){
	           		            grid.reload();
	           			    }else{
	           			    	grid.reload();
	           			    }
	           			}else{
	           			    WST.msg(json.msg,{icon:2});
	           			}
	           		});
	            }});
}
function illegal(id){
	var w = WST.open({type: 1,title:"商品违规原因",shade: [0.6, '#000'],border: [0],
	    content: '<textarea id="illegalRemarks" rows="7" style="width:96%" maxLength="200"></textarea>',
	    area: ['500px', '260px'],btn: ['确定', '关闭窗口'],
        yes: function(index, layero){
        	var illegalRemarks = $.trim($('#illegalRemarks').val());
        	if(illegalRemarks==''){
        		WST.msg('请输入违规原因 !', {icon: 5});
        		return;
        	}
        	var ll = WST.msg('数据处理中，请稍候...');
		    $.post(WST.U('admin/goods/illegal'),{id:id,illegalRemarks:illegalRemarks},function(data){
		    	layer.close(w);
		    	layer.close(ll);
		    	var json = WST.toAdminJson(data);
				if(json.status>0){
					WST.msg(json.msg, {icon: 1});
					grid.reload();
				}else{
					WST.msg(json.msg, {icon: 2});
				}
		   });
        }
	});
}

function initAuditGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/goods/auditByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        rowHeight:65,
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '&nbsp;', name: 'goodsName',width:60,align:'left',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
            	return "<img style='height:60px;width:60px;' src='"+WST.conf.ROOT+"/"+rowdata['goodsImg']+"'>";
            }},
	        { display: '商品名称', name: 'goodsName',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
	            return rowdata['goodsName'];
	        }},
	        { display: '商品编号', name: 'goodsSn',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['goodsSn']+"</div>";
	        }},
	        { display: '价格', name: 'shopPrice',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['shopPrice']+"</div>";
	        }},
	        { display: '所属店铺', name: 'shopName',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['shopName']+"</div>";
	        }},
	        { display: '所属分类', name: 'goodsCatName',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['goodsCatName']+"</div>";
	        }},
	        { display: '销量', name: 'saleNum',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['saleNum']+"</div>";
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<div class='goods-valign-m'><a target='_blank' href='"+WST.U("home/goods/detail","id="+rowdata['goodsId']+"&key="+rowdata['verfiycode'])+"'>查看</a> ";
	            if(WST.GRANT.DSHSP_04)h += "<a href='javascript:allow(" + rowdata['goodsId'] + ",0)'>审核通过</a> ";
	            if(WST.GRANT.DSHSP_03)h += "<a href='javascript:del(" + rowdata['goodsId'] + ",0)'>删除</a></div> "; 
	            return h;
	        }}
        ]
    });
}
function loadAuditGrid(){
	var params = WST.getParams('.j-ipt');
	params.areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	params.goodsCatIdPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats').join('_');
	grid.set('url',WST.U('admin/goods/auditByPage',params));
}
function allow(id,type){
	var box = WST.confirm({content:"您确定审核通过该商品吗?",yes:function(){
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('admin/goods/allow'),{id:id},function(data,textStatus){
        			layer.close(loading);
        			var json = WST.toAdminJson(data);
        			if(json.status=='1'){
        			    WST.msg(json.msg,{icon:1});
        			    layer.close(box);
        			    if(type==0){
        		            grid.reload();
        			    }else{
        			    	location.reload();
        			    }
        			}else{
        			    WST.msg(json.msg,{icon:2});
        			}
        		});
         }});
}

function initIllegalGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/goods/illegalByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        rowHeight:65,
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '&nbsp;', name: 'goodsName',width:60,align:'left',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
            	return "<img style='height:60px;width:60px;' src='"+WST.conf.ROOT+"/"+rowdata['goodsImg']+"'>";
            }},
	        { display: '商品名称', name: 'goodsName',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
	            return rowdata['goodsName'];
	        }},
	        { display: '商品编号', name: 'goodsSn',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['goodsSn']+"</div>";
	        }},
	        { display: '所属店铺', name: 'shopName',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['shopName']+"</div>";
	        }},
	        { display: '所属分类', name: 'goodsCatName',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['goodsCatName']+"</div>";
	        }},   
	        { display: '违规原因', name: 'illegalRemarks',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['illegalRemarks']+"</div>";
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<div class='goods-valign-m'><a target='_blank' href='"+WST.U("home/goods/detail","id="+rowdata['goodsId']+"&key="+rowdata['verfiycode'])+"'>查看</a> ";
	            if(WST.GRANT.WGSP_04)h += "<a href='javascript:allow(" + rowdata['goodsId'] + ",0)'>审核通过</a> ";
	            if(WST.GRANT.WGSP_03)h += "<a href='javascript:del(" + rowdata['goodsId'] + ",0)'>删除</a></div> "; 
	            return h;
	        }}
        ]
    });
}
function loadIllegalGrid(){
	var params = WST.getParams('.j-ipt');
	params.areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	params.goodsCatIdPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats').join('_');
	grid.set('url',WST.U('admin/goods/illegalByPage',params));
}