$(function(){
	stockByPage();
});
function stockByPage(p){
	$('#list').html('<tr><td colspan="11"><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...</td></tr>');
	var params = {};
	params = WST.getParams('.s-query');
	params.page = p;
	$.post(WST.U('home/goods/stockByPage'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status==1 && json.Rows){
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.Rows, function(html){
	       		$('#list').html(html);
	       		$('.j-goodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
	       	});
	       	if(json.TotalPage>1){
	       		laypage({
		        	 cont: 'pager', 
		        	 pages:json.TotalPage, 
		        	 curr: json.CurrentPage,
		        	 skin: '#e23e3d',
		        	 groups: 3,
		        	 jump: function(e, first){
		        		    if(!first){
		        		    	stockByPage(e.curr);
		        		    }
		        	    } 
		        });
	       	}else{
	       		$('#pager').empty();
	       	}
       	}  
	});
}
function toEdit(id,src){
	location.href = WST.U('home/goods/edit','id='+id+'&src='+src);
}
//双击修改
function toEditGoodsStock(id,type){
	$("#ipt_"+type+"_"+id).show();
	$("#span_"+type+"_"+id).hide();
	$("#ipt_"+type+"_"+id).focus();
	$("#ipt_"+type+"_"+id).val($("#span_"+type+"_"+id).html());
}
function endEditGoodsStock(type,id){
	$('#span_'+type+'_'+id).html($('#ipt_'+type+'_'+id).val());
	$('#span_'+type+'_'+id).show();
    $('#ipt_'+type+'_'+id).hide();
}
function editGoodsStock(id,type,goodsId){
	var number = $('#ipt_'+type+'_'+id).val();
	if($.trim(number)==''){
		WST.msg('库存不能为空', {icon: 5});
        return;
	}
	var params = {};
	params.id = id;
	params.type = type;
	params.goodsId = goodsId;
	params.number = number;
	$.post(WST.U('Home/Goods/editwarnStock'),params,function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status>0){
			$('#img_'+type+'_'+id).fadeTo("fast",100);
			endEditGoodsStock(type,id);
			$('#img_'+type+'_'+id).fadeTo("slow",0);
		}else{
			WST.msg(json.msg, {icon: 5}); 
		}
	});
}

function getCat(val){
  if(val==''){
  	$('#cat2').html("<option value='' >-请选择-</option>");
  	return;
  }
  $.post(WST.U('home/shopcats/listQuery'),{parentId:val},function(data,textStatus){
       var json = WST.toJson(data);
       var html = [],cat;
       html.push("<option value='' >-请选择-</option>");
       if(json.status==1 && json.list){
         json = json.list;
       for(var i=0;i<json.length;i++){
           cat = json[i];
           html.push("<option value='"+cat.catId+"'>"+cat.catName+"</option>");
        }
       }
       $('#cat2').html(html.join(''));
  });
}