/**WST.GRANR--权限函数，保留，请勿覆盖**/
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
	opts = $.extend(opts, {title:'系统提示',offset:'100px'}, options);
	return layer.confirm(opts.content,{icon: 3, title:opts.title,offset:opts.offset},options.yes,options.cancel);
}
WST.msg = function(msg, options, func){
	var opts = {};
	if(options){
		if(options.icon==1){
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
		opts = $.extend(opts,{time:2000,shade: [0.4, '#000000']},options);
		return layer.msg(msg, opts, func);
	}else{
		return layer.msg(msg, options);
	}
}
WST.pageSizeOptions = [50,100,150,200];
WST.pageSize = 50;
WST.toAdminJson = function(str){
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
WST.upload = function(opts){
	var _opts = {};
	_opts = $.extend(_opts,{auto: true,swf: WST.conf.ROOT +'/plugins/webuploader/Uploader.swf',server:WST.U('admin/index/uploadPic')},opts);
	var uploader = WebUploader.create(_opts);
	uploader.on('uploadSuccess', function( file,response ) {
	    var json = WST.toAdminJson(response._raw);
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
WST.getAreas = function(obj,id,val,fval,callback){
	var params = {};
	params.parentId = id;
	$("#"+obj).empty();
	$("#"+obj).html("<option value=''>请选择</option>");
	var s = [];
	if(fval!=''){
		s = fval.split(',');
		for(var i=0;i<s.length;i++){
			$("#"+s[i]).empty();
			$("#"+s[i]).html("<option value=''>请选择</option>");
		}
	}
	if(id == 0 || id == ''){
		s = fval.split(',');
		for(var i=0;i<s.length;i++){
			$("#"+s[i]).empty();
			$("#"+s[i]).html("<option value=''>请选择</option>");
		}
		return;
	}
	$.post(WST.U('admin/areas/listQuery'),params,function(data,textStatus){
		var json = WST.toAdminJson(data);
		if(json.status==1 && json.data){
			var opts,html=[];
			html.push("<option value=''>请选择</option>");
			for(var i=0;i<json.data.length;i++){
				opts = json.data[i];
				html.push('<option value="'+opts.areaId+'" '+((val==opts.areaId)?'selected':'')+'>'+opts.areaName+'</option>');
			}
			$("#"+obj).html(html.join(''));
			if(typeof(callback)=='function')callback();
		}
	});
}
$(function(){
	if(WST.conf && WST.conf.GRANT)WST.getGrants(WST.conf.GRANT);
	$('#wst-loading').remove();
})
WST.getGrants = function(grant){
	WST['GRANT'] = {};
	if(!grant)return;
	var str = grant.split(',');
	for(var i=0;i<str.length;i++){
		WST['GRANT'][str[i]] = true;
	}
}
/**
 * 把对象变成数组
 */
WST.arrayParams = function(v){
	var p = WST.getParams(v);
	var params = [];
	for(var key in p){
		params.push(key+"="+p[key]);
	}
	return params;
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
		$.post(WST.U('admin/goodscats/listQuery'),{parentId:opts.val},function(data,textStatus){
		     var json = WST.toAdminJson(data);
		     if(json.data && json.data.length>0){
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
	$.post(WST.U('admin/goodscats/listQuery'),{parentId:opts.val},function(data,textStatus){
	     var json = WST.toAdminJson(data);
	     if(json.data && json.data.length>0){
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
	$.post(WST.U('admin/areas/listQuery'),{parentId:opts.val},function(data,textStatus){
	     var json = WST.toAdminJson(data);
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
		$.post(WST.U('admin/areas/listQuery'),{parentId:opts.val},function(data,textStatus){
		     var json = WST.toAdminJson(data);
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