/**删除批量上传的图片**/
function delBatchUploadImg(obj){
	var c = WST.confirm({content:'您确定要删除商品图片吗?',yes:function(){
		$(obj).parent().remove("li");
		layer.close(c);
	}});
}
function lastGoodsCatCallback(opts){
	if(opts.isLast){
	    getSpecAttrs(opts.val);
	}else{
		$('#specsAttrBox').empty();
	}
}
/**初始化**/
function initEdit(){
	$('#tab').TabPanel({tab:0,callback:function(no){
		if(no==1){
			$('.j-specImg').children().each(function(){
				if(!$(this).hasClass('webuploader-pick'))$(this).css({width:'80px',height:'25px'});
			});
		}
		if(!initBatchUpload && no==2){
			initBatchUpload = true;
			var uploader = batchUpload({uploadPicker:'#batchUpload',uploadServer:WST.U('home/index/uploadPic'),formData:{dir:'goods',isWatermark:1,isThumb:1},uploadSuccess:function(file,response){
				var json = WST.toJson(response);
				if(json.status==1){
					$li = $('#'+file.id);
					$li.append('<input type="hidden" class="j-gallery-img" iv="'+json.savePath + json.thumb+'" v="' +json.savePath + json.name+'"/>');
					//$li.append('<span class="btn-setDefault">默认</span>' );
	                var delBtn = $('<span class="btn-del">删除</span>');
	                $li.append(delBtn);
	                delBtn.on('click',function(){
	                	delBatchUploadImg($(this),function(){
	                		uploader.removeFile(file);
	        				uploader.refresh();
	                	});
	    			});
	                $('.filelist li').css('border','1px solid rgb(59, 114, 165)');
				}else{
					WST.msg(json.msg,{icon:2});
				}
			}});
		}
		$('.btn-del').click(function(){
			delBatchUploadImg($(this),function(){
        		$(this).parent().remove();
        	});
		})
	}});
	WST.upload({
	  	  pick:'#goodsImgPicker',
	  	  formData: {dir:'goods',isWatermark:1,isThumb:1},
	  	  accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
	  	  callback:function(f){
	  		  var json = WST.toJson(f);
	  		  if(json.status==1){
	  			  $('#uploadMsg').empty().hide();
	              $('#preview').attr('src',WST.conf.ROOT+"/"+json.savePath+json.thumb);
	              $('#goodsImg').val(json.savePath+json.name);
	              $('#msg_goodsImg').hide();
	  		  }
		  },
		  progress:function(rate){
		      $('#uploadMsg').show().html('已上传'+rate+"%");
		  }
	});
	KindEditor.ready(function(K) {
		editor1 = K.create('textarea[name="goodsDesc"]', {
		  height:'350px',
		  width:'800px',
		  uploadJson : WST.conf.ROOT+'/home/goods/editorUpload',
		  allowFileManager : false,
		  allowImageUpload : true,
		  items:[
			          'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
			          'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
			          'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
			          'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
			          'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
			          'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|','image','multiimage','table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
			          'anchor', 'link', 'unlink', '|', 'about'
		  ],
		  afterBlur: function(){ this.sync(); }
		});
	});
	if(OBJ.goodsId>0){
		var goodsCatIds = OBJ.goodsCatIdPath.split('_');
		getBrands('brandId',goodsCatIds[0],OBJ.brandId);
		if(goodsCatIds.length>1){
			var objId = goodsCatIds[0];
			$('#cat_0').val(objId);
			var opts = {id:'cat_0',val:goodsCatIds[0],childIds:goodsCatIds,className:'j-goodsCats',afterFunc:'lastGoodsCatCallback'}
        	WST.ITSetGoodsCats(opts);
	    }
		getShopsCats('shopCatId2',OBJ.shopCatId1,OBJ.shopCatId2);
	}
	
}
/**获取本店分类**/
function getShopsCats(objId,pVal,objVal){
	$('#'+objId).empty();
	$.post(WST.U('home/shopcats/listQuery'),{parentId:pVal},function(data,textStatus){
	     var json = WST.toJson(data);
	     var html = [],cat;
	     html.push("<option value='' >-请选择-</option>");
	     if(json.status==1 && json.list){
	    	 json = json.list;
			 for(var i=0;i<json.length;i++){
			     cat = json[i];
			     html.push("<option value='"+cat.catId+"' "+((objVal==cat.catId)?"selected":"")+">"+cat.catName+"</option>");
			 }
	     }
	     $('#'+objId).html(html.join(''));
	});
}
/**获取品牌**/
function getBrands(objId,catId,objVal){
	$('#'+objId).empty();
	$.post(WST.U('home/brands/listQuery'),{catId:catId},function(data,textStatus){
	     var json = WST.toJson(data);
	     var html = [],cat;
	     html.push("<option value='' >-请选择-</option>");
	     if(json.status==1 && json.list){
	    	 json = json.list;
			 for(var i=0;i<json.length;i++){
			     cat = json[i];
			     html.push("<option value='"+cat.brandId+"' "+((objVal==cat.brandId)?"selected":"")+">"+cat.brandName+"</option>");
			 }
	     }
	     $('#'+objId).html(html.join(''));
	});
}
function toEdit(id,src){
	location.href = WST.U('home/goods/edit','id='+id+'&src='+src);
}
/**保存商品数据**/
function save(){
	$('#editform').isValid(function(v){
		if(v){
			var params = WST.getParams('.j-ipt');
			params.goodsCatId = WST.ITGetGoodsCatVal('j-goodsCats');
			params.specNum = specNum;
			var specsName,specImg;
			$('.j-speccat').each(function(){
				specsName = 'specName_'+$(this).attr('cat')+'_'+$(this).attr('num');
				specImg = 'specImg_'+$(this).attr('cat')+'_'+$(this).attr('num');
				if($(this)[0].checked){
					params[specsName] = $.trim($('#'+specsName).val());
					params[specImg] = $.trim($('#'+specImg).attr('v'));
				}
			});
			var gallery = [];
			$('.j-gallery-img').each(function(){
				gallery.push($(this).attr('v'));
			});
			params.gallery = gallery.join(',');
			var specsIds = [];
			var specidsmap = [];
			$('.j-ws').each(function(){
				specsIds.push($(this).attr('v'));
				specidsmap.push(WST.blank($(this).attr('sid'))+":"+$(this).attr('v'));
			});
			var specmap = [];
			for(var key in id2SepcNumConverter){
				specmap.push(key+":"+id2SepcNumConverter[key]);
			}
			params.specsIds = specsIds.join(',');
			params.specidsmap = specidsmap.join(',');
			params.specmap = specmap.join(',');
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('home/goods/'+((params.goodsId==0)?"toAdd":"toEdit")),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toJson(data);
		    	if(json.status=='1'){
		    		WST.msg(json.msg,{icon:1});
		    		location.href=WST.U('home/goods/'+src);
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}
var id2SepcNumConverter = {};
/**添加普通规格值**/
function addSpec(opts){
	var html = [];
	html.push('<div class="spec-item">',
	          '<input type="checkbox" class="j-speccat j-speccat_'+opts.catId+' j-spec_'+opts.catId+'_'+specNum+'" cat="'+opts.catId+'" num="'+specNum+'" onclick="javascript:addSpecSaleCol()" '+opts.checked+'/>',
	          '<input type="text" class="spec-ipt" id="specName_'+opts.catId+'_'+specNum+'" maxLength="50" value="'+WST.blank(opts.val)+'" onblur="batchChangeTxt(this.value,'+opts.catId+','+specNum+')"/>',
	          '<span class="item-del" onclick="delSpec(this,'+opts.catId+','+specNum+')"></span>',
	          '</div>');
	$(html.join('')).insertBefore('#specAddBtn_'+opts.catId);
	if(opts.itemId){
		id2SepcNumConverter[opts.itemId] = opts.catId+'_'+specNum;
	}
	
	specNum++;	
}
/**删除普通规格值**/
function delSpec(obj,catId,num){
	if($('.j-spec_'+catId+'_'+num)[0].checked){
		$('.j-spec_'+catId+'_'+num)[0].checked = false;
		addSpecSaleCol();
	}
	$(obj).parent().remove();
}
/**添加带图片的规格值**/
function addSpecImg(opts){
	var html = [];
	html.push('<tr>',
			    '<td>',
	            '<input type="checkbox" class="j-speccat j-speccat_'+opts.catId+' j-spec_'+opts.catId+'_'+specNum+'" cat="'+opts.catId+'" num="'+specNum+'" onclick="javascript:addSpecSaleCol()" '+opts.checked+'/>',
                '<input type="text" id="specName_'+opts.catId+'_'+specNum+'" maxLength="50" value="'+WST.blank(opts.val)+'" onblur="batchChangeTxt(this.value,'+opts.catId+','+specNum+')"/>',
                '</td>',
	            '<td id="uploadMsg_'+opts.catId+'_'+specNum+'">',
	            (opts.specImg)?'<img height="25"  width="25" id="specImg_'+opts.catId+'_'+specNum+'" src="'+WST.conf.ROOT+"/"+opts.specImg+'" v="'+opts.specImg+'"/>':"",
	            '</td><td><div id="specImgPicker_'+specNum+'" class="j-specImg">上传图片</div></td>'
	         );
	if($('#specTby').children().size()==0){
    	html.push('<td><input type="button" id="specImgBtn" value="新增" onclick="addSpecImg({catId:'+opts.catId+',checked:\'\'})"/></td>');
    }else{
    	html.push('<td><input type="button" id="specImgBtn" value="删除" onclick="delSpecImg(this,'+opts.catId+','+specNum+')"/></td>');
    }
    html.push('</tr>');
	$('#specTby').append(html.join(''));
	WST.upload({
		  num:specNum,
		  cat:opts.catId,
	  	  pick:'#specImgPicker_'+specNum,
	  	  formData: {dir:'goods',isThumb:1},
	  	  accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
	  	  callback:function(f){
	  		  var json = WST.toJson(f);
	  		  if(json.status==1){
	  			$('#uploadMsg_'+this.cat+"_"+this.num).html('<img id="specImg_'+this.cat+"_"+this.num+'" v="'+json.savePath+json.thumb+'" src="'+WST.conf.ROOT+"/"+json.savePath+json.thumb+'" height="25"  width="25"/>');
	  		  }
		  },
		  progress:function(rate){
		      $('#uploadMsg_'+this.cat+"_"+this.num).html('已上传'+rate+"%");
		  }
	});
	if(opts.itemId){
		id2SepcNumConverter[opts.itemId] = opts.catId+'_'+specNum;
	}
	specNum++;
}
/**删除带图片的规格值**/
function delSpecImg(obj,catId,num){
	if($('.j-spec_'+catId+'_'+num)[0].checked){
		$('.j-spec_'+catId+'_'+num)[0].checked = false;
		addSpecSaleCol();
	}
	$(obj).parent().parent().remove();
}
/**给销售规格表填上值**/
function fillSepcSale(){
	var ids = '',tmpids = [];
	for(var i=0;i<OBJ.saleSpec.length;i++){
		tmpids = [];
		ids = OBJ.saleSpec[i].specIds;
		ids = ids.split(':');
		for(var j=0;j<ids.length;j++){
			tmpids.push(id2SepcNumConverter[ids[j]]);
		}
		tmpids = tmpids.join('-');
		if(OBJ.saleSpec[i].isDefault)$('#isDefault_'+tmpids).attr('checked',true);
		$('#productNo_'+tmpids).val(OBJ.saleSpec[i].productNo);
		$('#marketPrice_'+tmpids).val(OBJ.saleSpec[i].marketPrice);
		$('#specPrice_'+tmpids).val(OBJ.saleSpec[i].specPrice);
		$('#specStock_'+tmpids).val(OBJ.saleSpec[i].specStock);
		$('#warnStock_'+tmpids).val(OBJ.saleSpec[i].warnStock);
		$('#saleNum_'+tmpids).val(OBJ.saleSpec[i].saleNum);
		$('#saleNum_'+tmpids).attr('sid',OBJ.saleSpec[i].id);
	}
}
/**生成销售规格表**/
function addSpecSaleCol(){
	//获取规格分类和规格值
	var catId,snum,specCols = {},obj = [];
	$('.j-speccat').each(function(){
		if($(this)[0].checked){
			catId = $(this).attr('cat');
			snum = $(this).attr('num');
			if(!specCols[catId]){
				specCols[catId] = [];
				specCols[catId].push({id:catId+"_"+snum,val:$.trim($('#specName_'+catId+"_"+snum).val())});
			}else{
				specCols[catId].push({id:catId+"_"+snum,val:$.trim($('#specName_'+catId+"_"+snum).val())});
			}
	    }
	});
	//创建表头
	$('.j-saleTd').remove();
	var html = [],specArray = [];;
	for(var key in specCols){
		html.push('<th class="j-saleTd">'+$('#specCat_'+key).html()+'</th>');
		specArray.push(specCols[key]);
	}
	if(html.length==0){
        $('#goodsStock').removeAttr('disabled');
		$('#shopPrice').removeAttr('disabled');
		$('#marketPrice').removeAttr('disabled');
		$('#warnStock').removeAttr('disabled');
		return;
	}
	$(html.join('')).insertBefore('#thCol');
	//组合规格值
	this.combined = function(doubleArrays){
        var len = doubleArrays.length;
        if (len >= 2) {
            var arr1 = doubleArrays[0];
            var arr2 = doubleArrays[1];
            var len1 = doubleArrays[0].length;
            var len2 = doubleArrays[1].length;
            var newlen = len1 * len2;
            var temp = new Array(newlen),ntemp;
            var index = 0;
            for (var i = 0; i < len1; i++) {
            	if(arr1[i].length){
            		for (var k = 0; k < len2; k++) {
            			ntemp = arr1[i].slice();
            			ntemp.push(arr2[k]);
		                temp[index] = ntemp;
		                index++;
            		}
            	}else{
	                for (var j = 0; j < len2; j++) {
	                    temp[index] = [arr1[i],arr2[j]];
	                    index++;
	                }
            	}
            }
            var newArray = new Array(len - 1);
            newArray[0] = temp;
            if (len > 2) {
                var _count = 1;
                for (var i = 2; i < len; i++) {
                    newArray[_count] = doubleArrays[i];
                    _count++;
                }
            }
            return this.combined(newArray);
        }else {
            return doubleArrays[0];
        }
    }
	
	var specsRows = this.combined(specArray);
	//生成规格值表
	html = [];
	var id=[],key=1,specHtml = [];
	var productNo = $('#productNo').val(),specProductNo = '';
	for(var i=0;i<specsRows.length;i++){
		id = [],specHtml = [];
		html.push('<tr class="j-saleTd">');
		
		if(specsRows[i].length){
			for(var j=0;j<specsRows[i].length;j++){
				id.push(specsRows[i][j].id);
				specHtml.push('<td class="j-td_'+specsRows[i][j].id+'">' + specsRows[i][j].val + '</td>');
	        }
		}else{
			id.push(specsRows[i].id);
			specHtml.push('<td>' + specsRows[i].val + '</td>');
		}
		id = id.join('-');
		if(OBJ.goodsId==0){
			specProductNo = productNo+'-'+key;
		}
		html.push('  <td><input type="radio" id="isDefault_'+id+'" name="defaultSpec" class="j-ipt" value="'+id+'"/></td>');
		html.push(specHtml.join(''));
		html.push('  <td><input type="text" class="spec-sale-goodsNo j-ipt" id="productNo_'+id+'" value="'+specProductNo+'" onblur="checkProductNo(this)"></td>',
	              '  <td><input type="text" class="spec-sale-ipt j-ipt" id="marketPrice_'+id+'"></td>',
	              '  <td><input type="text" class="spec-sale-ipt j-ipt" id="specPrice_'+id+'"></td>',
	              '  <td><input type="text" class="spec-sale-ipt j-ipt" id="specStock_'+id+'"></td>',
	              '  <td><input type="text" class="spec-sale-ipt j-ipt" id="warnStock_'+id+'"></td>',
	              '  <td class="j-ws" v="'+id+'" id="saleNum_'+id+'">0</td>',
	              '</tr>');
		key++;
	}
	$('#spec-sale-tby').append(html.join(''));
	//判断是否禁用商品价格和库存字段
	if($('#spec-sale-tby').html()!=''){
		$('#goodsStock').prop('disabled',true);
		$('#shopPrice').prop('disabled',true);
		$('#marketPrice').prop('disabled',true);
		$('#warnStock').prop('disabled',true);
	}
	//设置销售规格表值
	if(OBJ.saleSpec)fillSepcSale();
}
/**根据批量修改销售规格值**/
function batchChange(v,id){
	if($.trim(v)!=''){
		$('input[type=text][id^="'+id+'_"]').val(v);
	}
}
/**根据规格值修改 销售规格表 里的值**/
function batchChangeTxt(v,catId,num){
	$('.j-td_'+catId+"_"+num).each(function(){
		$(this).html(v);
	});
}
/**检测商品销售规格值是否重复**/
function checkProductNo(obj){
	v = $.trim(obj.value);
	var num = 0;
	$('input[type=text][id^="productNo_"]').each(function(){
		if(v==$.trim($(this).val()))num++;
	});
	if(num>1){
		WST.msg('已存在相同的货号',{icon:2});
		obj.value = '';
	}
}
/**获取商品规格和属性**/
function getSpecAttrs(goodsCatId){
	$('#specsAttrBox').empty();
	specNum = 0;
	$.post(WST.U('home/goods/getSpecAttrs'),{goodsCatId:goodsCatId},function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status==1 && json.data){
			var html = [],tmp,str;
			if(json.data.spec0 || json.data.spec1){
				html.push('<div class="spec-head">商品规格</div>');
				html.push('<div class="spec-body">');
				if(json.data.spec0){
					tmp = json.data.spec0;
					html.push('<div id="specCat_'+tmp.catId+'">'+tmp.catName+'</div>');
					html.push('<table><tbody id="specTby"></tbody></table>');
				}
				if(json.data.spec1){
					for(var i=0;i<json.data.spec1.length;i++){
						tmp = json.data.spec1[i];
						html.push('<div class="spec-line"></div>',
						          '<div id="specCat_'+tmp.catId+'">'+tmp.catName+'</div>',
						          '<div>',
						          '<input type="button" value="新增" id="specAddBtn_'+tmp.catId+'" onclick="javascript:addSpec({catId:'+tmp.catId+',checked:\'\'})"/>',
						          '</div>'
								);
					}
				}
				html.push('</div>');
				html.push('<div id="specSaleHead" class="spec-head">销售规格</div>',
				          '<table class="specs-sale-table">',
				          '  <thead id="spec-sale-hed">',
				          '   <tr>',
				          '     <th>推荐<br/>规格</th>',
				          '     <th id="thCol"><font color="red">*</font>货号</th>',
				          '     <th><font color="red">*</font>市场价<br/><input type="text" class="spec-sale-ipt" onblur="batchChange(this.value,\'marketPrice\')"></th>',
				          '     <th><font color="red">*</font>本店价<br/><input type="text" class="spec-sale-ipt" onblur="batchChange(this.value,\'specPrice\')"></th>',
				          '     <th><font color="red">*</font>库存<br/><input type="text" class="spec-sale-ipt" onblur="batchChange(this.value,\'specStock\')"></th>',
				          '     <th><font color="red">*</font>预警库存<br/><input type="text" class="spec-sale-ipt" onblur="batchChange(this.value,\'warnStock\')"></th>',
				          '     <th>销量</th>',
				          '   </tr>',
				          '  </thead>',
				          '  <tbody id="spec-sale-tby"></tbody></table>'
						);
			}
			if(json.data.attrs){
				html.push('<div class="spec-head">商品属性</div>');
				html.push('<div class="spec-body">');
				html.push('<table class="attr-table">');
				for(var i=0;i<json.data.attrs.length;i++){
					tmp = json.data.attrs[i];
					html.push('<tr><th width="120" nowrap>'+tmp.attrName+'：</th><td>');
					if(tmp.attrType==1){		
						str = tmp.attrVal.split(',');
						for(var j=0;j<str.length;j++){
						    html.push('<label><input type="checkbox" class="j-ipt" name="attr_'+tmp.attrId+'" value="'+str[j]+'"/>'+str[j]+'</label>');
						}
					}else if(tmp.attrType==2){
						html.push('<select name="attr_'+tmp.attrId+'" id="attr_'+tmp.attrId+'" class="j-ipt">');
						html.push('<option value="0">请选择</option>');
						str = tmp.attrVal.split(',');
						for(var j=0;j<str.length;j++){
							html.push('<option value="'+str[j]+'">'+str[j]+'</option>');
						}
						html.push('</select>');
					}else{
						html.push('<input type="text" name="attr_'+tmp.attrId+'" id="attr_'+tmp.attrId+'" class="spec-sale-text j-ipt"/>');
					}
					html.push('</td></tr>');
				}
				html.push('</table>');
				html.push('</div>');
			}
			$('#specsAttrBox').html(html.join(''));
			//如果是编辑的话，第一次要设置之前设置的值
			if(OBJ.goodsId>0 && specNum==0){
				//设置规格值
				if(OBJ.spec0){
					for(var i=0;i<OBJ.spec0.length;i++){
					   addSpecImg({catId:OBJ.spec0[i].catId,checked:'checked',val:OBJ.spec0[i].itemName,itemId:OBJ.spec0[i].itemId,specImg:OBJ.spec0[i].itemImg});
					}
				}
				if(OBJ.spec1){
					for(var i=0;i<OBJ.spec1.length;i++){
					    addSpec({catId:OBJ.spec1[i].catId,checked:'checked',val:OBJ.spec1[i].itemName,itemId:OBJ.spec1[i].itemId});
					}
				}
				addSpecSaleCol();
				//设置商品属性值
				var tmp = null;
				if(OBJ.attrs.length){
					for(var i=0;i<OBJ.attrs.length;i++){
						if(OBJ.attrs[i].attrType==1){
							tmp = OBJ.attrs[i].attrVal.split(',');
							WST.setValue("attr_"+OBJ.attrs[i].attrId,tmp);
						}else{
						    WST.setValue("attr_"+OBJ.attrs[i].attrId,OBJ.attrs[i].attrVal);
						}
					}
				}
				
			}
			//给没有初始化的规格初始化一个输入框
			if(json.data.spec0 && !$('.j-speccat_'+json.data.spec0.catId)[0]){
				addSpecImg({catId:json.data.spec0.catId,checked:''});
			}
			if(json.data.spec1){
				for(var i=0;i<json.data.spec1.length;i++){
					if(!$('.j-speccat_'+json.data.spec1[i].catId)[0])addSpec({catId:json.data.spec1[i].catId,checked:''});
				}
			}
			
		}
	});
}

function saleByPage(p){
	$('#list').html('<tr><td colspan="11"><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...</td></tr>');
	var params = {};
	params = WST.getParams('.s-query');
	params.key = $.trim($('#key').val());
	params.page = p;
	$.post(WST.U('home/goods/saleByPage'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status==1 && json.Rows){
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.Rows, function(html){
	       		$('#list').html(html);
	       		$('.j-lazyGoodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
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
		        		    	saleByPage(e.curr);
		        		    }
		        	    } 
		        });
	       	}else{
	       		$('#pager').empty();
	       	}
       	}  
	});
}
function auditByPage(p){
	$('#list').html('<tr><td colspan="11"><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...</td></tr>');
	var params = {};
	params = WST.getParams('.s-query');
	params.key = $.trim($('#key').val());
	params.page = p;
	$.post(WST.U('home/goods/auditByPage'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status==1 && json.Rows){
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.Rows, function(html){
	       		$('#list').html(html);
	       		$('.j-lazyGoodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
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
		        		      saleByPage(e.curr);
		        		 }
		        	 } 
		        });
	       	}else{
	       		$('#pager').empty();
	       	}
       	}  
	});
}
function storeByPage(p){
	$('#list1').html('<tr><td colspan="11"><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...</td></tr>');
	var params = {};
	params = WST.getParams('.s-query');
	params.key = $.trim($('#key1').val());
	params.page = p;
	$.post(WST.U('home/goods/storeByPage'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status==1 && json.Rows){
	       	var gettpl = document.getElementById('tblist1').innerHTML;
	       	laytpl(gettpl).render(json.Rows, function(html){
	       		$('#list1').html(html);
	       		$('.j-lazyGoodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
	       	});
	       	if(json.TotalPage>1){
	       		laypage({
		        	 cont: 'pager1', 
		        	 pages:json.TotalPage, 
		        	 curr: json.CurrentPage,
		        	 skin: '#e23e3d',
		        	 groups: 3,
		        	 jump: function(e, first){
		        		 if(!first){
		        			 storeByPage(e.curr);
		        		 }
		        	 } 
		        });
	       	}else{
	       		$('#pager1').empty();
	       	}
       	}  
	});
}
function illegalByPage(p){
	$('#list2').html('<tr><td colspan="4"><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...</td></tr>');
	var params = {};
	params = WST.getParams('.s-query');
	params.key = $.trim($('#key2').val());
	params.page = p;
	$.post(WST.U('home/goods/illegalByPage'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status==1 && json.Rows){
	       	var gettpl = document.getElementById('tblist2').innerHTML;
	       	laytpl(gettpl).render(json.Rows, function(html){
	       		$('#list2').html(html);
	       		$('.j-lazyGoodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.ROOT+'/'+WST.conf.GOODS_LOGO});
		    });
	       	if(json.TotalPage>1){
	       		laypage({
		        	 cont: 'pager2', 
		        	 pages:json.TotalPage, 
		        	 curr: json.CurrentPage,
		        	 skin: '#e23e3d',
		        	 groups: 3,
		        	 jump: function(e, first){
		        		 if(!first){
		        		      saleByPage(e.curr);
		        		 }
		        	 } 
		        });
	       	}else{
	       		$('#pager2').empty();
	       	}
       	}  
	});
}
function del(id,func){
	var c = WST.confirm({content:'您确定要删除商品吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'正在删除，请稍后...'});
		$.post(WST.U('home/goods/del'),{id:id},function(data,textStatus){
			layer.close(load);
		    var json = WST.toJson(data);
		    if(json.status==1){
		    	switch(func){
		    	   case 'store':storeByPage(0);break;
		    	   case 'sale':saleByPage(0);break;
		    	   case 'audit':auditByPage(0);break;
		    	   case 'illegal':illegalByPage(0);break;
		    	}
		    }else{
		    	WST.msg(json.msg,{icon:2});
		    }
		});
	}});
}

// 批量 上架/下架
function changeSale(i,func){
	var ids = WST.getChks('.chk');
	if(ids==''){
		WST.msg('请先选择商品!', {icon: 5});
		return;
	}
	var params = {};
	params.ids = ids;
	params.isSale = i;
	$.post(WST.U('home/goods/changeSale'), params, function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status=='1'){
			WST.msg('操作成功',{icon:1},(function(){
			   $('#all').prop('checked',false);
			   switch(func){
	    	       case 'store':storeByPage(0);break;
	    	       case 'sale':saleByPage(0);break;
	    	       case 'audit':auditByPage(0);break;
	    	  }
			}));
	    }else if(json.status=='-2'){
	    	WST.msg(json.msg, {icon: 5});
	    }else if(json.status=='2'){
	    	WST.msg(json.msg, {icon: 5},function(){
	    		switch(func){
		    	   case 'store':storeByPage(0);break;
		    	   case 'sale':saleByPage(0);break;
		    	   case 'audit':auditByPage(0);break;
		    	}
	    	});
	    }else if(json.status=='-3'){
	    	WST.msg(json.msg, {icon: 5,time:3000});
	    }else{
	    	WST.msg('操作失败!', {icon: 5});
	    }
	});
}

// 批量设置 精品/新品/推荐/热销
function changeGoodsStatus(isWhat,func){
	var ids = WST.getChks('.chk');
	if(ids==''){
		WST.msg('请先选择商品!', {icon: 5});
		return;
	}
	var params = {};
	params.ids = ids;
	params.is = isWhat;
	$.post(WST.U('home/goods/changeGoodsStatus'),params,function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status=='1'){
			WST.msg('设置成功',{icon:1},function(){
				   $('#all').prop('checked',false);
				   switch(func){
		    	   case 'store':storeByPage(0);break;
		    	   case 'sale':saleByPage(0);break;
		    	   case 'audit':auditByPage(0);break;
		    	  }
			});
		}else{
			WST.msg('设置失败',{icon:5});
		}
	});
}

// 双击设置 
function changSaleStatus(isWhat, obj, id){
	var params = {};
	status = $(obj).attr('status');
	params.status = status;
	params.id = id;
	switch(isWhat){
	   case 'r':params.is = "isRecom";break;
	   case 'b':params.is = "isBest";break;
	   case 'n':params.is = "isNew";break;
	   case 'h':params.is = "isHot";break;
	}
	var load = WST.load({msg:'请稍后...'});
	$.post(WST.U('home/goods/changSaleStatus'),params,function(data,textStatus){
		layer.close(load);
		var json = WST.toJson(data);
		if(json.status==1){
			if(status==0){
				$(obj).attr('status',1);
				$(obj).removeClass('wrong').addClass('right');
			}else{
				$(obj).attr('status',0);
				$(obj).removeClass('right').addClass('wrong');
			}
		}else{
			WST.msg('操作失败',{icon:5});
		}
	});
}

//双击修改
function toEditGoodsBase(fv,goodsId,flag){	
	if((fv==2 || fv==3) && flag==1){
		WST.msg('该商品存在商品属性，不能直接修改，请进入编辑页修改', {icon: 5});
		return;
	}else{
		$("#ipt_"+fv+"_"+goodsId).show();
		$("#span_"+fv+"_"+goodsId).hide();
		$("#ipt_"+fv+"_"+goodsId).focus();
		$("#ipt_"+fv+"_"+goodsId).val($("#span_"+fv+"_"+goodsId).html());
	}
	
}
function endEditGoodsBase(fv,goodsId){
	$('#span_'+fv+'_'+goodsId).html($('#ipt_'+fv+'_'+goodsId).val());
	$('#span_'+fv+'_'+goodsId).show();
    $('#ipt_'+fv+'_'+goodsId).hide();
}
function editGoodsBase(fv,goodsId){

	var vtext = $('#ipt_'+fv+'_'+goodsId).val();
	if($.trim(vtext)==''){
		if(fv==2){
			WST.msg('价格不能为空', {icon: 5});
		}else if(fv==3){
			WST.msg('库存不能为空', {icon: 5});
		}		
        return;
	}
	var params = {};
	(fv==2)?params.shopPrice=vtext:params.goodsStock=vtext;
	params.goodsId = goodsId;
	$.post(WST.U('Home/Goods/editGoodsBase'),params,function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status>0){
			$('#img_'+fv+'_'+goodsId).fadeTo("fast",100);
			endEditGoodsBase(fv,goodsId);
			$('#img_'+fv+'_'+goodsId).fadeTo("slow",0);
		}else{
			WST.msg('修改失败!', {icon: 5}); 
		}
	});
}

function benchDel(func,flag){
	if(flag==1){
		var ids = WST.getChks('.chk1');
	}else{
		var ids = WST.getChks('.chk');
	}
	
	if(ids==''){
		WST.msg('请先选择商品!', {icon: 5});
		return;
	}
	var params = {};
	params.ids = ids;
	var load = WST.load({msg:'请稍后...'});
	$.post(WST.U('home/goods/batchDel'),params,function(data,textStatus){
		layer.close(load);
		var json = WST.toJson(data);
		if(json.status=='1'){
			WST.msg('操作成功',{icon:1},function(){
				   $('#all').prop('checked',false);
				   switch(func){
		    	   case 'store':storeByPage(0);break;
		    	   case 'sale':saleByPage(0);break;
		    	   case 'audit':auditByPage(0);break;
		    	  }
			});
		}else{
			WST.msg('操作失败',{icon:5});
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