$(function(){
	$('.goodsImg2').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
	WST.dropDownLayer(".item",".dorp-down-layer");
	$('.item-more').click(function(){
		if($(this).attr('v')==1){
			$('.hideItem').show(300);
			$(this).find("span").html("收起");
			$(this).find("i").attr({"class":"drop-up"});
			$(this).attr('v',0);
		}else{
			$('.hideItem').hide(300);
			$(this).find("span").html("更多选项");
			$(this).find("i").attr({"class":"drop-down-icon"});
			$(this).attr('v',1);
		}
	});
	
	$(".item-more").hover(function(){
		if($(this).find("i").hasClass("drop-down-icon")){
			$(this).find("i").attr({"class":"down-hover"});
		}else{
			$(this).find("i").attr({"class":"up-hover"});
		}
		
	},function(){
		if($(this).find("i").hasClass("down-hover")){
			$(this).find("i").attr({"class":"drop-down"});
		}else{
			$(this).find("i").attr({"class":"drop-up"});
		}
	});
});

function goodsFilter(obj,vtype){
	if(vtype==1){
		$('#brand').val($(obj).attr('v'));
	}else if(vtype==2){
		var price = $(obj).attr('v');
		price = price.split('_');
		$('#sprice').val(price[0]);
		$('#eprice').val(price[1]);
	}else if(vtype==3){
		$('#v_'+$(obj).attr('d')).val($(obj).attr('v'));
		var vs = $('#vs').val();
		vs = (vs!='')?vs.split(','):[];
		vs.push($(obj).attr('d'));
		$('#vs').val(vs.join(','));
	}
	var ipts = WST.getParams('.sipt');
	if(vtype==4)ipts['order']='1';
	var params = [];
	for(var key in ipts){
		if(ipts[key]!='')params.push(key+"="+ipts[key]);
	}
	location.href=WST.U('home/goods/lists',params.join('&'));
}
function goodsOrder(orderby){
	if($('#orderBy').val()!=orderby){
		$('#order').val(1);
	}
	$('#orderBy').val(orderby);
	goodsFilter(null,0);
}



function removeFilter(id){
	if(id!='price'){
		$('#'+id).val('');
		if(id.indexOf('v_')>-1){
			id = id.replace('v_','');
			var vs = $('#vs').val();
			vs = (vs!='')?vs.split(','):[];
			var nvs = [];
			for(var i=0;i<vs.length;i++){
				if(vs[i]!=id)nvs.push(vs[i]);
			}
			$('#vs').val(nvs.join(','));
		}
	}else{
		$('#sprice').val('');
		$('#eprice').val('');
	}
	var ipts = WST.getParams('.sipt');
	var params = [];
	for(var key in ipts){
		if(ipts[key]!='')params.push(key+"="+ipts[key]);
	}
	location.href=WST.U('home/goods/lists',params.join('&'));
}
/*搜索列表*/
function searchFilter(obj,vtype){
	if(vtype==1){
		$('#brand').val($(obj).attr('v'));
	}else if(vtype==2){
		var price = $(obj).attr('v');
		price = price.split('_');
		$('#sprice').val(price[0]);
		$('#eprice').val(price[1]);
	}else if(vtype==3){
		$('#v_'+$(obj).attr('d')).val($(obj).attr('v'));
		var vs = $('#vs').val();
		vs = (vs!='')?vs.split(','):[];
		vs.push($(obj).attr('d'));
		$('#vs').val(vs.join(','));
	}
	var ipts = WST.getParams('.sipt');
	if(vtype==4)ipts['order']='1';
	var params = [];
	for(var key in ipts){
		if(ipts[key]!='')params.push(key+"="+ipts[key]);
	}
	location.href=WST.U('home/goods/search',params.join('&'));
}
function searchOrder(orderby){
	if($('#orderBy').val()!=orderby){
		$('#order').val(1);
	}
	$('#orderBy').val(orderby);
	searchFilter(null,0);
}


/*加入购物车*/
$('.goods').hover(function(){
	$(this).find('.sale-num').hide();
	$(this).find('.p-add-cart').show();
},function(){
	$(this).find('.sale-num').show();
	$(this).find('.p-add-cart').hide();
})



/*发货地*/
function gpanelOver(obj){
	var sid = $(obj).attr("id");

	var index = $(obj).attr('c');

	var ids = sid.split("_");
	var preid = ids[0]+"_"+ids[1];
	if(ids[2]==1){
		$("li[id^="+preid+"_]").hide();
		$("#"+sid).show();
	}else if(ids[2]==2){
		$('#fl_1_3').hide();
	}

	$("li[id^="+preid+"_]").removeClass("j-tab-selected"+index);
	$("#"+sid).addClass("j-tab-selected"+index);
	
	$("ul[id^="+preid+"_]").hide();
	$("#"+sid+"_pl").show();
}
function choiceArea(t,pid){
	var areaName = $(t).find('a').html();
	var parent = $(t).parent().attr('id');
	var ids = parent.split("_");
	var preid = "#"+ids[0]+"_"+ids[1]+"_"+ids[2];
	if(ids[2]==3){
		$(preid).find('a').html(areaName);
		// 执行发货地筛选
		$('#areaId').val(pid);
		var ipts = WST.getParams('.sipt');
		var params = [];
		for(var key in ipts){
			if(ipts[key]!='')params.push(key+"="+ipts[key]);
		}
		var url = ($(t).attr('search')==1)?'home/goods/search':'home/goods/lists';
		location.href=WST.U(url,params.join('&'));
	}else{
		// 替换当前选中地区
		$(preid).find('a').html(areaName);
		$(preid).removeClass('j-tab-selected'+ids[1]);


		var next = parseInt(ids[2])+1;
		var nextid = "#"+ids[0]+"_"+ids[1]+"_"+next;
		$(nextid).show();
		$(nextid).addClass("j-tab-selected"+ids[1]);
		// 替换下级地图标题
		$(nextid).html('<a href="javascript:void(0)">请选择</a>');

		// 获取下级地区信息
		$.post(WST.U('home/areas/listQuery'),{parentId:pid},function(data){
			// 判断搜索页面
			var search = $(t).attr('search');
			if(search==1){search = 'search="1"';}
			
			var json = WST.toJson(data);
			if(json.status==1){
				var html = '';
				$(json.data).each(function(k,v){

					html +='<li onclick="choiceArea(this,'+v.areaId+')" '+search+' ><a href="javascript:void(0)">'+v.areaName+'</a></li>';
				});
				$(nextid+"_pl").html(html);
			}
		});

		// 隐藏当前地区,显示下级地区
		var preid = ids[0]+"_"+ids[1];
		$("ul[id^="+preid+"_]").hide();
		$(nextid+"_pl").show();
	}
}

