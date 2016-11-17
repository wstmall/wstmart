var grid;
$(function(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/attributes/pageQuery'),
		pageSize:100,
		pageSizeOptions:[100],
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '属性名称', name: 'attrName', isSort: false,align: 'left'},
	        { display: '所属商品分类', name: 'goodsCatNames', isSort: false,align: 'left'},
	        { display: '属性类型', name: 'attrType', isSort: false,align: 'left',render :function(rowdata, rowindex, value){
	        	return (value==1)?'多选项':(value==2?'下拉框':'输入框');
	        }},
	        { display: '属性选项', name: 'attrVal', isSort: false,align: 'left'},
	        { display: '是否显示', name: 'isShow', isSort: false,width: 100,render :function(rowdata, rowindex, value){
	        	return (value==1)?'<span style="cursor:pointer" onclick="toggleIsShow('+rowdata['attrId']+', 0)">显示</span>':(value==0?'<span style="cursor:pointer" onclick="toggleIsShow('+rowdata['attrId']+', 1)">隐藏</span>':'');
	        }},
	        { display: '排序号', name: 'attrSort', isSort: false,align: 'left'},
	        { display: '操作', name: 'op',isSort: false,width: 200,render: function (rowdata, rowindex, value){
	            var h = "";
	        	if(WST.GRANT.SPSX_02)h += "<a href='javascript:toEdit("+ rowdata['attrId']+")'>修改</a> ";
	        	if(WST.GRANT.SPSX_03)h += "<a href='javascript:toDel(" + rowdata['attrId'] + ")'>删除</a> "; 
	            return h;	          
	        }}
        ]
    });
});

//------------------属性类型---------------//
function toEdit(attrId){
	$("select[id^='bcat_0_']").remove();
	$('#attrForm').get(0).reset();
	$.post(WST.U('admin/attributes/get'),{attrId:attrId},function(data,textStatus){
        var json = WST.toAdminJson(data);
        WST.setValues(json);
        if(json.goodsCatId>0){
        	var goodsCatPath = json.goodsCatPath.split("_");
        	$('#bcat_0').val(goodsCatPath[0]);
        	var opts = {id:'bcat_0',val:goodsCatPath[0],childIds:goodsCatPath,className:'goodsCats'}
        	WST.ITSetGoodsCats(opts);
        }
		var title =(attrId==0)?"新增":"编辑";
		var box = WST.open({title:title,type:1,content:$('#attrBox'),area: ['750px', '320px'],btn:['确定','取消'],yes:function(){
			$('#attrForm').submit();
		}});
		$('#attrForm').validator({
			rules: {
				attrType: function() {
		            return ($('#attrType').val()!='0');
		        }
		    },
			fields: {
			 	'attrName': {rule:"required",msg:{required:'请输入属性名称'}},
			 	'attrVal': 'required(attrType)'
			},
			valid: function(form){
			    var params = WST.getParams('.ipt');
			    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
			    params.goodsCatId = WST.ITGetGoodsCatVal('goodsCats');
			 	$.post(WST.U('admin/attributes/'+((params.attrId==0)?"add":"edit")),params,function(data,textStatus){
			 		layer.close(loading);
			    	var json = WST.toAdminJson(data);
					if(json.status=='1'){
						WST.msg("操作成功",{icon:1});
						grid.reload();
						layer.close(box);
				  	}else{
				    	WST.msg(json.msg,{icon:2});
					}
			 	});
			}
		});

	});
}
function loadGrid(){
	var keyName = $("#keyName").val();
	var goodsCatPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats');
	grid.set('url',WST.U('admin/attributes/pageQuery',{"keyName":keyName,"goodsCatPath":goodsCatPath.join('_')}));
}

function toDel(attrId){
	var box = WST.confirm({content:"您确定要删除该属性吗?",yes:function(){
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/attributes/del'),{attrId:attrId},function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json.status=='1'){
				WST.msg("操作成功",{icon:1});
				layer.close(box);
				grid.reload();
			}else{
				WST.msg(json.msg,{icon:2});
			}
		});
	}});
}

function toggleIsShow( attrId, isShow){
	$.post(WST.U('admin/attributes/setToggle'), {'attrId':attrId, 'isShow':isShow}, function(data, textStatus){
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
			WST.msg("操作成功",{icon:1});
			grid.reload();
		}else{
			WST.msg(json.msg,{icon:2});
		}
	})
}

function changeArrType(v){
	if(v>0){
		$('#attrValTr').show();
	}else{
		$('#attrValTr').hide();
	}
}
