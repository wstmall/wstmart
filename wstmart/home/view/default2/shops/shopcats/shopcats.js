function addCat(obj,p,catNo){
	var html = new Array();
	if(typeof(obj)=="number"){
		$("#cat_list_tab").append($("#cat_p_tr").html());
	}else{
		var className = (p==0)?"tr_c_new":"tr_"+catNo+" tr_0";
		var gettpl = $("#cat_c_tr").html();
		laytpl(gettpl).render({"className":className,"p":p}, function(html){
			$(obj).parent().parent().parent().append(html);
		});
	}
	$('.wst-shop-but').show();
}

function delCatObj(obj,vk){
	if(vk==1){
		$(obj).parent().parent().parent().remove();
	}else{
		$(obj).parent().parent().remove();
	}
	if($(".tr_0").size()==0 && $(".tbody_new").size()==0)$('.wst-shop-but').hide();
}

function treeCatOpen(obj,id){
    if( $(obj).attr('class').indexOf('active') > -1 ){
    	$(obj).removeClass('active');
        $(obj).html('<img class="wst-lfloat" style="margin-top:-3px;" src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/seller_icon_sq.png">');
        $('.tree_'+id).hide();
    }else{
    	$(obj).addClass('active');
        $(obj).html('<img class="wst-lfloat" style="margin-top:-3px;" src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/seller_icon_zk.png">');
        $('.tree_'+id).show();
    }
}

function delCat(id){
	var box = WST.confirm({content:"您确定要删除该商品分类吗？",yes:function(){
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('home/shopcats/del'),{id:id},function(data,textStatus){
			layer.close(loading);
			var json = WST.toJson(data);
			if(json.status=='1'){
				WST.msg("操作成功",{icon:1});
				layer.close(box);
				location.reload();
			}else{
				WST.msg(json.msg,{icon:2});
			}
		});
	}});
}


function batchSaveCats(){
	var params = {};
	var fristNo = 0;
	var secondNo = 0;
	$(".tbody_new").each(function(){
		secondNo = 0;
		var pobj = $(this).find(".tr_new");
		params['catName_'+fristNo] = $.trim(pobj.find(".catname").val());
		if(params['catName_'+fristNo]==''){
			WST.msg('请输入商品分类名称!', {icon: 5});
			return;
		}
		params['catSort_'+fristNo] = pobj.find(".catsort").val();
		params['catShow_'+fristNo] = pobj.find(".catshow").prop("checked")?1:0
		$(this).find(".tr_c_new").each(function(){
			params['catId_'+fristNo+'_'+secondNo] = fristNo;
			params['catName_'+fristNo+'_'+secondNo] = $.trim($(this).find(".catname").val());
			if(params['catName_'+fristNo+'_'+secondNo]==''){
				WST.msg('请输入商品分类名称!', {icon: 5});
				return;
			}
			params['catSort_'+fristNo+'_'+secondNo] = $(this).find(".catsort").val();
			params['catShow_'+fristNo+'_'+secondNo] = $(this).find(".catshow").prop("checked")?1:0
			params['catSecondNo_'+fristNo] = ++secondNo;		
		});
		params['fristNo'] = ++fristNo;
	});
	var otherNo = 0;
	$(".tr_0").each(function(){
		params['catId_o_'+otherNo] = $(this).attr('catId');
		params['catName_o_'+otherNo] = $.trim($(this).find(".catname").val());
		if(params['catName_o_'+otherNo]==''){
			WST.msg('请输入商品分类名称!', {icon: 5});
			return;
		}
		params['catSort_o_'+otherNo] = $(this).find(".catsort").val();
		params['catShow_o_'+otherNo] = $(this).find(".catshow").prop("checked")?1:0;
		params['otherNo'] = ++otherNo;
	});
	$.post(WST.U('home/shopcats/batchSaveCats'),params,function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status==1){
			WST.msg('新增成功!', {icon: 1,time:500},function(){
				location.reload();
			}); 
		}else{
			WST.msg('新增失败!', {icon: 5}); 
		}
	});
}


function editCatName(obj){
	$.post(WST.U('home/shopcats/editName'),{"id":$(obj).attr('dataId'),"catName":obj.value},function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status=='1'){
			WST.msg('操作成功!',{icon: 1,time:500});
		}else{
			WST.msg('操作失败!', {icon: 5});
		}
	});
}
function editCatSort(obj){
	$.post(WST.U('home/shopcats/editSort'),{"id":$(obj).attr('dataId'),"catSort":obj.value},function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status=='1'){
			WST.msg('操作成功!',{icon: 1,time:500});
		}else{
			WST.msg('操作失败!', {icon: 5});
		}
	});
}

function changeCatStatus(isShow,id,pid){
	var params = {};
		params.id = id;
		params.isShow = isShow;
		params.pid = pid;
	$.post(WST.U('home/shopcats/changeCatStatus'),params,function(data,textStatus){
		location.reload();  
	});
	
}