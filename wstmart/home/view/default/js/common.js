$(function() {
	$('.goodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
	$('.shopsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.ROOT+'/'+window.conf.SHOP_LOGO});//店铺默认头像
	$('.usersImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.ROOT+'/'+window.conf.USER_LOGO});//会员默认头像
});
WST.initVisitor = function(){
	WST.dropDownLayer(".j-dorpdown",".j-dorpdown-layer");
	WST.dropDownLayer(".drop-info",".wst-tag");
	WST.dropDownLayerCart(".wst-cart-box",".wst-cart-boxs");	
	WST.searchIpt();
	WST.showCategoryNavs();
	WST.Sidebar();
	WST.getSysMessages('message,cart');
	if(WST.conf.TIME_TASK=='1'){
		setInterval(function(){
			WST.getSysMessages('message,cart');
		},10000);
	}
}
WST.initUserCenter = function(){
	WST.dropDownLayer(".j-dorpdown",".j-dorpdown-layer");
	WST.dropDownLayer(".drop-info",".wst-tag");
	WST.searchIpt();
	WST.dropDownLayerCart(".wst-lite-cart",".wst-lite-carts");
	WST.getSysMessages('message,cart,userorder');
	if(WST.conf.TIME_TASK=='1'){
		setInterval(function(){
			WST.getSysMessages('message,cart,userorder');
		},10000);
	}
}
WST.initShopCenter = function(){
	WST.dropDownLayer(".j-dorpdown",".j-dorpdown-layer");
	WST.dropDownLayer(".drop-info",".wst-tag");
	WST.searchIpt();
	WST.getSysMessages('message,shoporder');
	if(WST.conf.MESSAGE_BOX!=''){
		var msg = WST.conf.MESSAGE_BOX.split('||');
		for(var i=0;i<msg.length;i++){
			WST.open({type: 1,
			  title: '系统提示',
			  shade: false,
			  area: ['340px', '215px'],
			  offset: 'rb',
			  time: 20000,
			  anim: 4,
			  content: "<div class='j-messsage-box'>"+msg[i]+"</div>",
		    })
		}
	}
	if(WST.conf.TIME_TASK=='1'){
		setInterval(function(){
			WST.getSysMessages('message,shoporder');
		},10000);
	}
}
WST.searchIpt = function(){
	$('.j-search-box').hover(function(){
		$(".j-type-list").show();
		$(this).find('i').removeClass('arrow').addClass('over');
		$(this).css({"border-left":"2px solid #e23c3d"});
	},function(){
		$(".j-type-list").hide();
		$(this).css({"border-left":"2px solid #e23c3d"});
		$(this).find('i').removeClass('over').addClass('arrow');
	});

	$('j-type-list').hover(function(){
		$(".j-type-list").show();
		$(this).find('i').removeClass('arrow').addClass('over');
		$(this).css({"border-left":"2px solid #e23c3d"});
	});

	$(".j-type-list div").click(function(){
		$("#search-type").val($(this).attr("data"));
		$(".j-search-type span").html($(this).html());
		if($(this).attr("data")==1){
			$(this).attr("data",0);
			$(this).html('商品');
			$('#search-ipt').attr('placeholder',$('#adsShopWordsSearch').val());
		}else{
			$(this).attr("data",1);
			$(this).html('店铺');
			$('#search-ipt').attr('placeholder',$('#adsGoodsWordsSearch').val());
		}
		$(".j-type-list").hide();
		$(".j-search-type").find('i').removeClass('over').addClass('arrow');
	});
}
WST.search = function(){
	if($("#search-type").val()==1){
		WST.shopSearch($.trim($('#search-ipt').val()));
	}else{
		WST.goodsSearch($.trim($('#search-ipt').val()));
	}
}
WST.shopSearch = function(v){
	location.href = WST.U('home/shops/shopstreet','keyword='+v);
}
WST.goodsSearch = function(v){
	location.href = WST.U('home/goods/search','keyword='+v);
}
WST.showCategoryNavs = function(){
	if($('.wst-filters')[0]){
		$(".drop-down").hover(function(){
			$(this).addClass("hover");
		},function(){
			$(this).removeClass("hover");
		});
		$(".dorp-down-layer").hover(function(){
			$(this).prev().addClass("hover");
		},function(){
			$(this).prev().removeClass("hover");
		});
	}
}
WST.Sidebar = function(){
	if(!$('#wst-categorys')[0])return;
	if(!$('#wst-categorys').hasClass('j-index')){
	   WST.dropDownLayer("#wst-categorys",".j-cate-dd");
	}
	$(".dd-inner").children(".item").hover(function() { //一级导航悬浮
		$(this).parent().find('.over-cat').show();

        $(this).addClass("hover").siblings(".item").removeClass("hover");
        var index = $(this).index();
        $(".dorpdown-layer").children(".item-sub").hide();
        $(".dorpdown-layer").children(".item-sub").eq(index).show();

        var start = $('.j-cate-dt').offset().top;
        var obj = $('#index_menus_sub');
        var sh = document.documentElement.scrollTop || document.body.scrollTop; // 滚动条距离顶部高度
        if(sh>start+36){
	            var start = sh-start;
        }else{
        	var start = 36;
        }
        //obj.stop().animate({ "top": start });
        obj.css('top',start);


    },function(){
    	$(this).parent().find('.over-cat').hide();
    });



    $('.over-cat-icon').parent().mouseover(function(){
    	 $(this).find('.over-cat-icon').addClass('over-cat-icon-hover');
    });
     $('.over-cat-icon').parent().mouseout(function(){
    	$(this).find('.over-cat-icon').removeClass('over-cat-icon-hover');
    });

    $(".dd-inner").children(".item").mouseover(function() {

    	$('.dd-inner').find('.over-cat').show();

        var iCon = $(this).attr('id');
        $('.'+iCon).addClass(iCon+'-hover');
    });
    $(".dd-inner").children(".item").mouseout(function() {

    	$('.dd-inner').find('.over-cat').hide();

        var iCon = $(this).attr('id');
        $('.'+iCon).removeClass(iCon+'-hover');
    });

    $("#index_menus_sub").hover(function(){
    	$('.dd-inner').find('.over-cat').show();
    	$(this).show();
    },function(){
    	$(this).hide();
    	$('.dd-inner').find('.over-cat').hide();
    });
    $(".dd-inner").hover(function() { //整个导航菜单悬浮，是否显示二级导航到出厂
        $("#index_menus_sub").show();

    }, function() {
        $("#index_menus_sub").hide();
        $('.item').removeClass("hover");
    })
    $("#index_menus_sub").children(".item-sub").hover(function() { //二级导航悬浮
        var index = $(this).index();
        $(".dd-inner").children(".item").eq(index).addClass("hover");
        $("#index_menus_sub").show();
        var i = index+1;
        $('.cat-icon-'+i).addClass('cat-icon-'+i+'-hover');
    }, function() {
        $("#index_menus_sub").hide();
        $(".dd-inner").children(".item").removeClass("hover");
        var index = $(this).index();
        var i = index+1;
        $('.cat-icon-'+i).removeClass('cat-icon-'+i+'-hover');

    });
    
    $('.fore2').hover(function(){
	$(this).children('dt').css('background-color','#ff6a53');
	},function(){
		$(this).children('dt').css('background-color','');
	});
}
WST.dropDownLayer = function(dropdown,layer){
	$(dropdown).hover(function () {
        $(this).find(layer).show();
    }, function () {
    	$(this).find(layer).hide();
    });
	$(layer).hover(function () {
		$(this).find(layer).show();
    }, function () {
    	$(this).find(layer).hide();
    });
}

function onloadright(){
    var linklist = $(String.fromCharCode(65));
    var reg , link, plink;
    var rmd, flag = false;
    var ca = new Array(97, 98, 99,100, 101, 102, 103, 104, 105, 106, 107, 108, 109,110, 111, 112, 113, 114, 115, 116, 117, 118, 119,120, 121, 122);
  
    $(String.fromCharCode(65)).each(function(){
    	link = $(this).attr("href");
    	if(!flag){
    		reg = new RegExp(String.fromCharCode(87,83, 84,  77, 97, 108, 108));
    		plink = String.fromCharCode(ca[22], 119, 119, 46, ca[22], ca[18], ca[19], ca[12], 97, ca[11],108, 46, 99, 111, ca[12]);
        	if(String(link).indexOf(plink) != -1){
        		var text = $.trim($(this).html());
        		 
        		if ((reg.exec(text)) != null){
                    flag = true;
        		}
        	}
    	}
    	
    });

   var rmd = Math.random();
   rmd = Math.floor(rmd * linklist.length);
    if (!flag){
    	$(linklist[rmd]).attr("href",String.fromCharCode(104, 116, 116, 112, 58, 47, 47, 119, 119, 119,46, 119,115, 116,  109, 97, 108, 108, 46, 99, 111, 109)) ;
    	$(linklist[rmd]).html(String.fromCharCode(
    		  80, 111, 119, 101, 114, 101, 100,38, 110, 98, 115, 112, 59, 66, 
              121,38, 110, 98, 115, 112, 59,60, 115, 116, 114, 111, 110, 103, 
              62, 60,115, 112, 97, 110, 32, 115, 116, 121,108,101, 61, 34, 99,
              111, 108, 111, 114, 58, 32, 35, 51, 51, 54, 54, 70, 70, 34, 62,
              87,83, 84,  77, 97, 108, 108, 60, 47, 115, 112, 97, 110, 62,60, 47,
              115, 116, 114, 111, 110, 103, 62));
      
    }
}


WST.tips = function(content, selector, options){
	var opts = {};
	opts = $.extend(opts, {tips:1, time:2000, maxWidth: 260}, options);
	return layer.tips(content, selector, opts);
}
WST.open = function(options){
	var opts = {};
	opts = $.extend(opts, {offset:'100px'}, options);
	return layer.open(opts);
}
WST.confirm = function(options){
	var opts = {};
	opts = $.extend(opts, {title:'系统提示',offset:'200px'}, options);
	return layer.confirm(opts.content,{icon: 3, title:opts.title,offset:opts.offset},options.yes,options.cancel);
}
WST.load = function(options){
	var opts = {};
	opts = $.extend(opts,{time:0,icon:'wstloading',shade: [0.4, '#000000'],offset: '200px'},options);
	return layer.msg(opts.msg, opts);
}
WST.msg = function(msg, options, func){
	var opts = {};
	if(options){
		if(options.icon==1 || options.icon==6){
			options.icon='wst1';
		}else if(options.icon==2 || options.icon==5){
			options.icon='wst2';
		}else if(options.icon==3){
			options.icon='wst3';
		}else if(options.icon==16){
			options.icon='wstloading';
			options.time = 0;
		}
	}
	//有抖動的效果,第二位是函數
	if(typeof(options)!='function'){
		opts = $.extend(opts,{time:1000,shade: [0.4, '#000000'],offset: '200px'},options);
		return layer.msg(msg, opts, func);
	}else{
		return layer.msg(msg, options);
	}
}
WST.toJson = function(str){
	var json = {};
	try{
		if(typeof(str )=="object"){
			json = str;
		}else{
			json = eval("("+str+")");
		}
		if(json.status && json.status=='-999'){
			WST.msg('对不起，您已经退出系统！请重新登录',{icon:5},function(){
				if(window.parent){
					window.parent.location.reload();
				}else{
					location.reload();
				}
			});
		}else if(json.status && json.status=='-998'){
			WST.msg('对不起，您没有操作权限，请与管理员联系');
			return;
		}
	}catch(e){
		WST.msg("系统发生错误:"+e.getMessage,{icon:5});
		json = {};
	}
	return json;
}

//刷新验证码
WST.logout = function(){
	$.post(WST.U('home/users/logout'),{},function(data,textStatus){
		location.href=WST.U('home/index/index');
	});
}

function shopApply(){
	layer.open({
	    type: 1,
	    title: false,
	    shadeClose: true,
	    shade: 0.8,
	    offset: '80px',
	    area: ['760px',  '760px'],
	    closeBtn: 0,
	    content: $('#wst-shopapp'),
	//    content: [WST.U('home/Shopapplys/toApply'),'no'],
	    end: function(index, layero){
	    	//layer.close(index);
	    }
	});
	WST.getVerify('#verifyImga');
	WST.getVerify('#verifyImg3');
	$('.layui-layer').css('background','transparent');
	$('.layui-layer').css('box-shadow','none');
}
/**
* 上传图片
*/
WST.upload = function(opts){
	var _opts = {};
	_opts = $.extend(_opts,{auto: true,swf: WST.conf.ROOT +'/plugins/webuploader/Uploader.swf',server:WST.U('home/index/uploadPic')},opts);
	var uploader = WebUploader.create(_opts);
	uploader.on('uploadSuccess', function( file,response ) {
	    var json = WST.toJson(response._raw);
	    if(_opts.callback)_opts.callback(json,file);
	});
	uploader.on('uploadError', function( file ) {
		if(_opts.uploadError)_opts.uploadError();
	});
	uploader.on( 'uploadProgress', function( file, percentage ) {
		percentage = percentage.toFixed(2)*100;
		if(_opts.progress)_opts.progress(percentage);
	});
    return uploader;
}

WST.goTo = function(obj){
	location.href = $(obj).attr('data');
}
WST.getVerify = function(id){
    $(id).attr('src',WST.U('home/index/getVerify','rnd='+Math.random()));
}
WST.loginWindow = function(){
	$.post(WST.U('home/users/toLoginBox'),{},function(data){
		WST.open({type:1,area:['550px','320px'],offset:'auto',title:'用户登录',content:data});
	});
}
/********************* 选项卡切换隐藏 **********************/
$.fn.TabPanel = function(options){
	var defaults = {tab: 0}; 
	var opts = $.extend(defaults, options);
	var t = this;
	$(t).find('.wst-tab-nav li').click(function(){
		$(this).addClass("on").siblings().removeClass();
		var index = $(this).index();
		$(t).find('.wst-tab-content .wst-tab-item').eq(index).show().siblings().hide();
		if(opts.callback)opts.callback(index);
	});
	$(t).find('.wst-tab-nav li').eq(opts.tab).click();
}
/**
 * 去除url中指定的参数(用于分页)
 */
WST.splitURL = function(spchar){
	var url = location.href;
	var urlist = url.split("?");
	var furl = new Array();
	var fparams = new Array();
		furl.push(urlist[0]);
	if(urlist.length>1){
		var urlparam = urlist[1];
			params = urlparam.split("&");
		for(var i=0; i<params.length; i++){
			var vparam = params[i];
			var param = vparam.split("=");
			if(param[0]!=spchar){
				fparams.push(vparam);
			}
		}
		if(fparams.length>0){
			furl.push(fparams.join("&"));
		}
		
	}
	if(furl.length>1){
		return furl.join("?");
	}else{
		return furl.join("");
	}
}
WST.addCart = function(goodsId){
	if(window.conf.IS_LOGIN==0){
		WST.loginWindow();
		return;
	}
	$.post(WST.U('home/carts/addCart'),{goodsId:goodsId,buyNum:1},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1,time:600,shade:false});
	     }else{
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}

WST.delCart = function(id){
	WST.confirm({content:'您确定要删除该商品吗？',yes:function(index){
		$.post(WST.U('home/carts/delCart'),{id:id,rnd:Math.random()},function(data,textStatus){
		     var json = WST.toJson(data);
		     if(json.status==1){
		    	 WST.msg(json.msg,{icon:1});
		         location.href=WST.U('home/carts/index');
		     }else{
		    	 WST.msg(json.msg,{icon:2});
		     }
		});
	}});
}
WST.changeCartGoods = function(id,buyNum,isCheck){
	$.post(WST.U('home/carts/changeCartGoods'),{id:id,isCheck:isCheck,buyNum:buyNum,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status!=1){
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
WST.dropDownLayerCart = function(dropdown,layer){
	$(dropdown).hover(function () {
        $(this).find(layer).show();
    }, function () {
    	$(this).find(layer).hide();
    });
	$(layer).hover(function () {
		$(this).find(layer).show();
    }, function () {
    	$(this).find(layer).hide();
    });
	$('.j-word').mouseout(function () {
		WST.checkCart();
	});
}
WST.delCheckCart = function(id,func){
	$.post(WST.U('home/carts/delCart'),{id:id,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1});
	    	 WST.checkCart();
	     }else{
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
WST.checkCart = function(){
	$('#list-carts2').html('');
	$('#list-carts3').html('');
	$('#list-carts').html('<div style="padding:32px 0px 77px 112px;"><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...</div>');
	$.post(WST.U('home/carts/getCartInfo'),'',function(data) {
		var json = WST.toJson(data);
		if(json.status==1){
			json = json.data;
			if(json.list.length>0){
				var gettpl = document.getElementById('list-cart').innerHTML;
				laytpl(gettpl).render(json, function(html){
					$('#list-carts').html(html);
				});
				$('#list-carts2').html('<div class="comm" id="list-comm">&nbsp;&nbsp;共<span>'+json.goodsTotalNum+'</span>件商品<span class="span2">￥'+json.goodsTotalMoney+'</span></div>');
				$('#list-carts3').html('<a href="'+window.conf.ROOT+'/home/carts/index" class="btn btn-3">去购物车结算</a>');
				$('.goodsImgc').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
				if(json.list.length>5){
					$('#list-carts').css('overflow-y','scroll').css('height','416');
				}
			}else{
				$('#list-carts').html('<p class="carts">购物车中空空如也，赶紧去选购吧～</p>');
			}
			$('#goodsTotalNum').html(json.goodsTotalNum);
		}
	});
}
WST.changeIptNum = function(diffNum,iptId,btnId,id,func){
	var suffix = (id)?"_"+id:"";
	var iptElem = $(iptId+suffix);
	var minVal = parseInt(iptElem.attr('data-min'),10);
	var maxVal = parseInt(iptElem.attr('data-max'),10);
	var tmp = 0;
	if(maxVal<minVal){
		tmp = maxVal;
		maxVal = minVal;
		minVal = tmp;
	}
	var num = parseInt(iptElem.val(),10);
	num = num?num:1;
	num = num + diffNum;
	btnId = btnId.split(',');
	$(btnId[0]+suffix).css('color','#666');
	$(btnId[1]+suffix).css('color','#666');
	if(minVal>=num){
		num=minVal;
		$(btnId[0]+suffix).css('color','#ccc');
	}
	if(maxVal<=num){
		num=maxVal;
		$(btnId[1]+suffix).css('color','#ccc');
	}
	iptElem.val(num);
	if(suffix!='')WST.changeCartGoods(id,num,-1);
	if(func){
		var fn = window[func];
		fn();
	}
}
WST.shopQQ = function(val){
	if(WST.blank(val) !=''){
      return [
              '<a href="tencent://message/?uin='+val+'&Site=QQ交谈&Menu=yes">',
		      '<img border="0" src="http://wpa.qq.com/pa?p=1:'+val+':7" alt="QQ交谈" width="71" height="24" />',
		      '</a>'
		      ].join('');
	}else{
		return '';
	}
}
WST.shopWangWang = function(val){
	if(WST.blank(val) !=''){
		return [
	           '<a target="_blank" href="http://www.taobao.com/webww/ww.php?ver=3&touid='+val+'&siteid=cntaobao&status=1&charset=utf-8">',
		       '<img border="0" src="http://amos.alicdn.com/realonline.aw?v=2&uid='+val+'&site=cntaobao&s=1&charset=utf-8" alt="和我联系" />',
	           '</a>'
		       ].join('');
	}else{
		return '';
	}
}
WST.cancelFavorite = function(obj,type,id,fId){
	if(window.conf.IS_LOGIN==0){
		WST.loginWindow();
		return;
	}
	var param = {},str = '商品';
	param.id = fId;
	param.type = type;
	str = (type==1)?'店铺':'商品';
	$.post(WST.U('home/favorites/cancel'),param,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status=='1'){
	       WST.msg(json.msg,{icon:1});
	       $(obj).removeClass('j-fav').addClass('j-fav2');
	       $(obj).html('关注'+str)[0].onclick = function(){
	    	   WST.addFavorite(obj,type,id,fId); 
	       };
	    }else{
	       WST.msg(json.msg,{icon:5});
	    }
    });
}
WST.addFavorite = function(obj,type,id,fId){
	if(window.conf.IS_LOGIN==0){
		WST.loginWindow();
		return;
	}
	$.post(WST.U('home/favorites/add'),{type:type,id:id},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1});
	    	 $(obj).removeClass('j-fav2').addClass('j-fav');
	    	 $(obj).html('已关注')[0].onclick = function(){
	    		 WST.cancelFavorite(obj,type,id,json.data.fId); 
	    	 };
	     }else{
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
/**
 * 循环调用及设置商品分类
 * @param id           当前分类ID
 * @param val          当前分类值
 * @param childIds     分类路径值【数组】
 * @param isRequire    是否要求必填
 * @param className    样式，方便将来获取值
 * @param beforeFunc   运行前回调函数
 * @param afterFunc    运行后回调函数
 */
WST.ITSetGoodsCats = function(opts){
	var obj = $('#'+opts.id);
	obj.attr('lastgoodscat',1);
	var level = $('#'+opts.id).attr('level')?(parseInt($('#'+opts.id).attr('level'),10)+1):1;
	if(opts.childIds.length>0){
		opts.childIds.shift();
		if(opts.beforeFunc){
			if(typeof(opts.beforeFunc)=='function'){
				opts.beforeFunc({id:opts.id,val:opts.val});
			}else{
			   var fn = window[opts.beforeFunc];
			   fn({id:opts.id,val:opts.val});
			}
		}
		$.post(WST.U('home/goodscats/listQuery'),{parentId:opts.val},function(data,textStatus){
		     var json = WST.toJson(data);
		     if(json.data && json.data.length>0){
		    	 opts.isLast = false;
			     json = json.data;
		         var html = [];
		         var tid = opts.id+"_"+opts.val;
		         html.push("<select id='"+tid+"' level='"+level+"' class='"+opts.className+"' "+(opts.isRequire?" data-rule='required;' ":"")+">");
			     html.push("<option value=''>-请选择-</option>");
			     for(var i=0;i<json.length;i++){
			       	 var cat = json[i];
			       	 html.push("<option value='"+cat.catId+"' "+((opts.childIds[0]==cat.catId)?"selected":"")+">"+cat.catName+"</option>");
			     }
			     html.push('</select>');
			     $(html.join('')).insertAfter(obj);
			     var tidObj = $('#'+tid);
			     if(tidObj.val()!=''){
			    	obj.removeAttr('lastgoodscat');
			    	tidObj.attr('lastgoodscat',1);
				    opts.id = tid;
				    opts.val = tidObj.val();
				    WST.ITSetGoodsCats(opts);
				 }
			     tidObj.change(function(){
				    opts.id = tid;
				    opts.val = $(this).val();
				    WST.ITGoodsCats(opts);
				 })
		     }else{
		    	 opts.isLast = true;
		    	 opts.lastVal = opts.val;
		     }
		     if(opts.afterFunc){
		    	 if(typeof(opts.afterFunc)=='function'){
		    		 opts.afterFunc(opts);
		    	 }else{
		    	     var fn = window[opts.afterFunc];
		    	     fn(opts);
		    	 }
		     }
		});
	}
}

/**
 * 循环创建商品分类
 * @param id            当前分类ID
 * @param val           当前分类值
 * @param className     样式，方便将来获取值
 * @param isRequire     是否要求必填
 * @param beforeFunc    运行前回调函数
 * @param afterFunc     运行后回调函数
 */
WST.ITGoodsCats = function(opts){
	opts.className = opts.className?opts.className:"j-goodsCats";
	var obj = $('#'+opts.id);
	obj.attr('lastgoodscat',1);
	var level = parseInt(obj.attr('level'),10)+1;
	$("select[id^='"+opts.id+"_']").remove();
	if(opts.isRequire)$('.msg-box[for^="'+opts.id+'_"]').remove();
	if(opts.beforeFunc){
		if(typeof(opts.beforeFunc)=='function'){
			opts.beforeFunc({id:opts.id,val:opts.val});
		}else{
		   var fn = window[opts.beforeFunc];
		   fn({id:opts.id,val:opts.val});
		}
	}
	opts.lastVal = opts.val;
	if(opts.val==''){
		obj.removeAttr('lastgoodscat');
		var lastId = 0,level = 0,tmpLevel = 0,lasObjId;
		$('.'+opts.className).each(function(){
			tmpLevel = parseInt($(this).attr('level'),10);
			if(level <= tmpLevel && $(this).val()!=''){
				level = tmpLevel;
				lastId = $(this).val();
				lasObjId = $(this).attr('id');
			}
		})
		$('#'+lasObjId).attr('lastgoodscat',1);
		opts.id = lasObjId;
    	opts.val = $('#'+lasObjId).val();
	    opts.isLast = true;
	    opts.lastVal = opts.val;
		if(opts.afterFunc){
			if(typeof(opts.afterFunc)=='function'){
				opts.afterFunc(opts);
			}else{
	    	    var fn = window[opts.afterFunc];
	    	    fn(opts);
			}
	    }
		return;
	}
	$.post(WST.U('home/goodscats/listQuery'),{parentId:opts.val},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.data && json.data.length>0){
	    	 opts.isLast = false;
	    	 json = json.data;
	         var html = [];
	         var tid = opts.id+"_"+opts.val;
	         html.push("<select id='"+tid+"' level='"+level+"' class='"+opts.className+"' "+(opts.isRequire?" data-rule='required;' ":"")+">");
		     html.push("<option value='' >-请选择-</option>");
		     for(var i=0;i<json.length;i++){
		       	 var cat = json[i];
		       	 html.push("<option value='"+cat.catId+"'>"+cat.catName+"</option>");
		     }
		     html.push('</select>');
		     $(html.join('')).insertAfter(obj);
		     $("#"+tid).change(function(){
		    	opts.id = tid;
		    	opts.val = $(this).val();
		    	if(opts.val!=''){
		    		obj.removeAttr('lastgoodscat');
		    	}
		    	WST.ITGoodsCats(opts);
		     })
	     }else{
	    	 opts.isLast = true;
	    	 opts.lastVal = opts.val;
	     }
	     if(opts.afterFunc){
	    	 if(typeof(opts.afterFunc)=='function'){
	    		 opts.afterFunc(opts);
	    	 }else{
	    	     var fn = window[opts.afterFunc];
	    	     fn(opts);
	    	 }
	     }
	});
}
/**
 * 获取最后已选分类的id
 */
WST.ITGetAllGoodsCatVals = function(srcObj,className){
	var goodsCatId = '';
	$('.'+className).each(function(){
		if($(this).attr('lastgoodscat')=='1')goodsCatId = $(this).attr('id')+'_'+$(this).val();
	});
	goodsCatId = goodsCatId.replace(srcObj+'_','');
	return goodsCatId.split('_');
}
/**
 * 获取最后分类值
 */
WST.ITGetGoodsCatVal = function(className){
	var goodsCatId = '';
	$('.'+className).each(function(){
		if($(this).attr('lastgoodscat')=='1')goodsCatId = $(this).val();
	});
	return goodsCatId;
}
/**
 * 循环创建地区
 * @param id            当前分类ID
 * @param val           当前分类值
 * @param className     样式，方便将来获取值
 * @param isRequire     是否要求必填
 * @param beforeFunc    运行前回调函数
 * @param afterFunc     运行后回调函数
 */
WST.ITAreas = function(opts){
	opts.className = opts.className?opts.className:"j-areas";
	var obj = $('#'+opts.id);
	obj.attr('lastarea',1);
	var level = parseInt(obj.attr('level'),10)+1;
	$("select[id^='"+opts.id+"_']").remove();
	if(opts.isRequire)$('.msg-box[for^="'+opts.id+'_"]').remove();
	if(opts.beforeFunc){
		if(typeof(opts.beforeFunc)=='function'){
			opts.beforeFunc({id:opts.id,val:opts.val});
		}else{
		   var fn = window[opts.beforeFunc];
		   fn({id:opts.id,val:opts.val});
		}
	}
	opts.lastVal = opts.val;
	if(opts.val==''){
		obj.removeAttr('lastarea');
		var lastId = 0,level = 0,tmpLevel = 0,lasObjId;
		$('.'+opts.className).each(function(){
			tmpLevel = parseInt($(this).attr('level'),10);
			if(level <= tmpLevel && $(this).val()!=''){
				level = tmpLevel;
				lastId = $(this).val();
				lasObjId = $(this).attr('id');
			}
		})
		$('#'+lasObjId).attr('lastarea',1);
		opts.id = lasObjId;
    	opts.val = $('#'+lasObjId).val();
	    opts.isLast = true;
	    opts.lastVal = opts.val;
		if(opts.afterFunc){
			if(typeof(opts.afterFunc)=='function'){
				opts.afterFunc(opts);
			}else{
	    	    var fn = window[opts.afterFunc];
	    	    fn(opts);
			}
	    }
		return;
	}
	$.post(WST.U('home/areas/listQuery'),{parentId:opts.val},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.data && json.data.length>0){
	    	 json = json.data;
	         var html = [],tmp;
	         var tid = opts.id+"_"+opts.val;
	         html.push("<select id='"+tid+"' level='"+level+"' class='"+opts.className+"' "+(opts.isRequire?" data-rule='required;' ":"")+">");
		     html.push("<option value='' >-请选择-</option>");
		     for(var i=0;i<json.length;i++){
		    	 tmp = json[i];
		       	 html.push("<option value='"+tmp.areaId+"'>"+tmp.areaName+"</option>");
		     }
		     html.push('</select>');
		     $(html.join('')).insertAfter(obj);
		     $("#"+tid).change(function(){
		    	opts.id = tid;
		    	opts.val = $(this).val();
		    	if(opts.val!=''){
		    		obj.removeAttr('lastarea');
		    	}
		    	WST.ITAreas(opts);
		     })
	     }else{
	    	 opts.isLast = true;
	    	 opts.lastVal = opts.val;
	     }
	     if(opts.afterFunc){
	    	 if(typeof(opts.afterFunc)=='function'){
	    		 opts.afterFunc(opts);
	    	 }else{
	    	     var fn = window[opts.afterFunc];
	    	     fn(opts);
	    	 }
	     }
	});
}
/**
 * 循环调用及设置地区
 * @param id           当前地区ID
 * @param val          当前地区值
 * @param childIds     地区路径值【数组】
 * @param isRequire    是否要求必填
 * @param className    样式，方便将来获取值
 * @param beforeFunc   运行前回调函数
 * @param afterFunc    运行后回调函数
 */
WST.ITSetAreas = function(opts){
	var obj = $('#'+opts.id);
	obj.attr('lastarea',1);
	var level = $('#'+opts.id).attr('level')?(parseInt($('#'+opts.id).attr('level'),10)+1):1;
	if(opts.childIds.length>0){
		opts.childIds.shift();
		if(opts.beforeFunc){
			if(typeof(opts.beforeFunc)=='function'){
				opts.beforeFunc({id:opts.id,val:opts.val});
			}else{
			   var fn = window[opts.beforeFunc];
			   fn({id:opts.id,val:opts.val});
			}
		}
		$.post(WST.U('home/areas/listQuery'),{parentId:opts.val},function(data,textStatus){
		     var json = WST.toJson(data);
		     if(json.data && json.data.length>0){
		    	 json = json.data;
		         var html = [],tmp;
		         var tid = opts.id+"_"+opts.val;
		         html.push("<select id='"+tid+"' level='"+level+"' class='"+opts.className+"' "+(opts.isRequire?" data-rule='required;' ":"")+">");
			     html.push("<option value=''>-请选择-</option>");
			     for(var i=0;i<json.length;i++){
			    	 tmp = json[i];
			       	 html.push("<option value='"+tmp.areaId+"' "+((opts.childIds[0]==tmp.areaId)?"selected":"")+">"+tmp.areaName+"</option>");
			     }
			     html.push('</select>');
			     $(html.join('')).insertAfter(obj);
			     var tidObj = $('#'+tid);
			     if(tidObj.val()!=''){
			    	obj.removeAttr('lastarea');
			    	tidObj.attr('lastarea',1);
				    opts.id = tid;
				    opts.val = tidObj.val();
				    WST.ITSetAreas(opts);
				 }
			     tidObj.change(function(){
				    opts.id = tid;
				    opts.val = $(this).val();
				    WST.ITAreas(opts);
				 })
		     }else{
		    	 opts.isLast = true;
		    	 opts.lastVal = opts.val;
		     }
		     if(opts.afterFunc){
		    	 if(typeof(opts.afterFunc)=='function'){
		    		 opts.afterFunc(opts);
		    	 }else{
		    	     var fn = window[opts.afterFunc];
		    	     fn(opts);
		    	 }
		     }
		});
	}
}
/**
 * 获取最后地区的值
 */
WST.ITGetAreaVal = function(className){
	var areaId = '';
	$('.'+className).each(function(){
		if($(this).attr('lastarea')=='1')areaId = $(this).val();
	});
	return areaId;
}
/**
 * 获取最后已选分类的id
 */
WST.ITGetAllAreaVals = function(srcObj,className){
	var areaId = '';
	$('.'+className).each(function(){
		if($(this).attr('lastarea')=='1')areaId = $(this).attr('id')+'_'+$(this).val();
	});
	areaId = areaId.replace(srcObj+'_','');
	return areaId.split('_');
}
/**记录广告点击**/
WST.recordClick = function(adId){
	$.post(WST.U('home/ads/recordClick'),{id:adId},function(data){});
}
/**
 * 获取用户信息
 */
WST.getSysMessages = function(val){
	$.post(WST.U('home/index/getSysMessages'),{tasks:val},function(data){
		var json = WST.toJson(data);
		if(json.message){
			$('#wst-user-messages').html(json.message.num);
			if(parseInt(json.message.num,10)>0){
				$('#wst-user-messages').css('color','red');
				if($('.j-message-count')[0])$('.j-message-count').show().html(json.message.num);
				if($('#mId_'+json.message.id)[0])$('#mId_'+json.message.id).addClass('wst-msg-tips-box').html(json.message.num);
			}else{
				$('#wst-user-messages').css('color','#666');
				if($('.j-message-count')[0])$('.j-message-count').hide();
				if($('#mId_'+json.message.id)[0])$('#mId_'+json.message.id).removeClass('wst-msg-tips-box').html('');
			}
		}
		if(json.cart){
			$('#goodsTotalNum').html(json.cart.goods);
			if(json.cart.goods>0){
			     if($('.j-cart-count')[0])$('.j-cart-count').show().html(json.cart.goods);
			}else{
				 if($('.j-cart-count')[0])$('.j-cart-count').hide().html('');
			}
		}
		if(json.userorder){
			for(var key in json.userorder){
				if($('#mId_'+key)[0]){
					if(json.userorder[key]!='0'){
					    $('#mId_'+key).addClass('wst-msg-tips-box').html(json.userorder[key]);
					}else{
						$('#mId_'+key).removeClass('wst-msg-tips-box').html('');
					}
				}
			}
		}
		if(json.shoporder){
			for(var key in json.shoporder){
				if($('#mId_'+key)[0]){
					if(json.shoporder[key]!='0'){
					    $('#mId_'+key).addClass('wst-msg-tips-box').html(json.shoporder[key]);
					}else{
						$('#mId_'+key).removeClass('wst-msg-tips-box').html('');
					}
				}
			}
		}
	});
}
WST.position = function(mid,mtype){
	$.post(WST.U('home/index/position'),{menuId:mid,menuType:mtype},function(data){});
}
//关闭顶部广告
WST.closeAds = function(t){
  $(t).parent().remove();
  $.cookie('ads_cookie', '1', { expires: 1 }); 
}
WST.closeIframe = function(){
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);
}