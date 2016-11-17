/**
 * 因为ligerui的tree用起来有些问题没解决，所以赶时间临时参考效果做一个，后期看情况扩展，使用参数上参考ligerui.^_^.
 * params: {
 * url:'xxxx/xxx/xxx',
 * width:'100%',
 * headerRowHeight:28,
 * rowHeight:28,
 * params:{xxx:xxx,xxx:xxx}
 * rownumbers:true,
 * columns:[{display:'分类名称',name:'catName',id:'catId',align:'left',width:'20%',render:function(item){}}]
 *}
 */
$.fn.WSTGridTree = function(options){
	var radomId = (new Date().getTime()+Math.round(Math.random()*700));
	var defaults = {id:'wst_'+radomId,tbodyId:'wst_tbody_'+radomId,width:'100%',headerRowHeight:28,rowHeight:28,nodeNum:0,params:{},level:0}; 
	var opts = $.extend(defaults, options);
	var jqObj = $(this);
	var treeKeyId;
	var createTBHead = function(){
		var head = [],htmp,rowAlign,rowWidth;
		head.push('<table class="l-grid-header-table wst-grid-tree" cellspacing="0" cellpadding="0" width="'+opts.width+'"><tbody id="'+opts.tbodyId+'"><tr height="'+opts.headerRowHeight+'" class="l-grid-hd-row wst-grid-tree-hd">');
		if(opts.rownumbers)head.push('<td class="l-grid-hd-cell l-grid-hd-cell-rownumbers" style="width:26px">&nbsp;</td>');
		for(var i=0;i<opts.columns.length;i++){
			htmp = opts.columns[i];
			rowWidth = htmp.width?('width="'+htmp.width+'"'):"";
			if(htmp.id){
				treeKeyId = htmp.id;
			    head.push('<td class="l-grid-hd-cell" style="text-align:left;padding-left:5px;" '+rowWidth+'>'+htmp.display+'</td>');
			}else{
				rowAlign = (htmp.align?(' style="text-align:'+htmp.align+'" '):'');
				head.push('<td class="l-grid-hd-cell" '+rowAlign+' '+rowWidth+'>'+htmp.display+'</td>');
			}
		}
		head.push('</tr></tbody></table>');
		$(jqObj).html(head.join(''));
	}
	var loadData = function(){
		var popts = $.extend(opts,{pid:opts.tbodyId,params:{},level:0});
		loadRow(popts);
	}
	var loadRow = function(_opts){
		$("tr[id^='"+opts.pid+"_']").remove();
		$.getJSON(_opts.url,_opts.params,function(data,textStatus){
			if(data.Rows){
			    var body,htmp,ttmp,val,rowAlign,rowWidth,nodeId,prefix;
			    data = data.Rows;
			    var lastNodeId = opts.pid;
			    for(var i=0;i<data.length;i++){
					 ttmp = data[i];
					 nodeId = _opts.pid+"_"+opts.nodeNum;
					 opts.nodeNum++;
					 body = [];
					 body.push('<tr id="'+nodeId+'" height="'+opts.rowHeight+'" class="l-grid-row wst-grid-tree-row j-'+ttmp[treeKeyId]+'" dataid="'+ttmp[treeKeyId]+'" pdataid="'+nodeId+'" lv="'+_opts.level+'">');
					 if(opts.rownumbers)body.push('<td class="l-grid-row-cell l-grid-row-cell-rownumbers" style="width:26px">'+(i+1)+'</td>');
					 for(var j=0;j<opts.columns.length;j++){
						 htmp = opts.columns[j];
						 if(htmp.render){
							 val = htmp.render(ttmp);
						 }else{
							 val = ttmp[htmp.name];
						 }
						 rowWidth = htmp.width?('width="'+htmp.width+'"'):"";
						 if(htmp.id){
							 prefix = '';
							 for(var k=0;k<_opts.level;k++){
								 prefix+='<div class="l-grid-tree-space"></div>';
							 }
							 prefix+='<div class="l-grid-tree-space l-grid-tree-link l-grid-tree-link-close wst-tree-img" dataid="'+ttmp[treeKeyId]+'" pdataid="'+nodeId+'" lv="'+_opts.level+'"></div>';
							 body.push('<td class="l-grid-row-cell" style="text-align:left" '+rowWidth+'>'+prefix+val+'</td>');
						 }else{
							 rowAlign = (htmp.align?(' style="text-align:'+htmp.align+'" '):'');
							 body.push('<td class="l-grid-row-cell" '+rowAlign+' '+rowWidth+'>'+val+'</td>');
						 }
					 }
					 body.push('</tr>');
					 $(body.join('')).insertAfter($('#'+lastNodeId));
					 lastNodeId = nodeId;
					 $('#'+nodeId+" .wst-tree-img").click(function(){
						 if($(this).hasClass('l-grid-tree-link-close')){
							 $(this).removeClass('l-grid-tree-link-close').addClass('l-grid-tree-link-open');
							 if($("tr[id^='"+$(this).attr('pdataid')+"_']").size()==0){
								 _opts.pid = $(this).attr('pdataid');
								 _opts.params[treeKeyId] = $(this).attr('dataid');
								 _opts.level = parseInt($(this).attr('lv'),10)+1;
								 loadRow(_opts);
							 }else{
								 $("tr[id^='"+$(this).attr('pdataid')+"_']").each(function(){
									 $(this).show();
								 })
							 }
						 }else{
							 $(this).removeClass('l-grid-tree-link-open').addClass('l-grid-tree-link-close');
							 $("tr[id^='"+$(this).attr('pdataid')+"_']").each(function(){
								 $(this).hide();
							 })
						 }
						 changeRowColor();
						 changeLinenumber();
					 })
				 }
			    changeRowColor();
			    changeLinenumber();
			 }
		})
	}
	var changeRowColor = function(){
		var even = false;
		$('.wst-grid-tree-row').each(function(){
			$(this).removeClass('bg-color');
			if($(this).is(':visible')){
				if(even)$(this).addClass('bg-color');
				even = !even;
				$(this).click(function(){
					$(this).addClass('row-selected').siblings().removeClass('row-selected');
					$(this).addClass('row-selected').siblings().each(function(){
						if($(this).hasClass('bg-color2') && !$(this).hasClass('row-selected'))$(this).removeClass('bg-color2').addClass('bg-color');
					})
					
				});
				$(this).live({ 
					mouseover:function(){ 
						if($(this).hasClass('row-selected'))$(this).removeClass('row-selected').addClass('row-selected2');
						if($(this).hasClass('bg-color'))$(this).removeClass('bg-color').addClass('bg-color2');
						$(this).addClass('row-hover');
				    }, 
				    mouseout:function(){ 
				    	$(this).removeClass('row-hover');
				    	if($(this).hasClass('row-selected2'))$(this).removeClass('row-selected2').addClass('row-selected');
						if($(this).hasClass('bg-color2') && !$(this).hasClass('row-selected'))$(this).removeClass('bg-color2').addClass('bg-color');
				    } 
				}) 
			}	
		})
	}
	var changeLinenumber = function(){
		var i=1;
		$('.wst-grid-tree-row').each(function(){
		    if($(this).is(':visible'))$(this).find('.l-grid-row-cell-rownumbers').html(i++);
		});
	}
	createTBHead();
	loadData();
	return {reload:function(nodeId){
		if(nodeId && nodeId>0){
			var node = $('.j-'+nodeId);
			var topts = {params:{}};
			topts['pid'] = node.attr('id');
			topts['params'][treeKeyId] = nodeId;
			topts['level'] = parseInt(node.attr('lv'),10)+1;
			var popts = $.extend(opts,topts);
			loadRow(popts);
		}else{
			loadData();
		}
	}}
}