var grid;
var combo;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/articles/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '<input type="checkbox" id="chk" onclick="WST.checkChks(this,\'.chk\')"/>', name: 'articleId',width:25,isSort: false,
                render: function (rowdata){
                	return '<input type="checkbox" value="'+rowdata["articleId"]+'" class="chk"/>';
            }},
            { display: '文章ID', name: 'articleId',width:50,isSort: false},
	        { display: '标题', name: 'articleTitle',isSort: false},
            { display: '分类', name: 'catName',width: 120,isSort: false,},
            { display: '是否显示', width: 80, name: 'isShow',isSort: false,
                render: function (item)
                {
                    if (parseInt(item.isShow) == 1) return '<span style="cursor:pointer;" onclick="toggleIsShow('+item["isShow"]+','+item["articleId"]+');">显示</span>';
                    return '<span style="cursor:pointer;" onclick="toggleIsShow('+item["isShow"]+','+item["articleId"]+');">隐藏</span>';
                }
            },
            { display: '最后编辑者',name: 'staffName',width: 100, isSort: false},
	        { display: '创建时间', name: 'createTime',width: 150,isSort: false},
	        { display: '操作', name: 'op',width: 100,isSort: false,
	        	render: function (rowdata){
		            var h = "";
		            if(WST.GRANT.WZGL_02)h += "<a href='javascript:toEdit("+rowdata["articleId"]+")'>修改</a> ";
		            if(WST.GRANT.WZGL_03)h += "<a href='javascript:toDel("+rowdata["articleId"]+")'>删除</a> "; 
		            return h;
	        	}
	        }
        ]
    });

}

function initCombo(){
    combo = $("#catIds").ligerComboBox({
        width: 210,
        selectBoxWidth: 200,
        selectBoxHeight: 300,valueField:'catId',textField: 'catName',treeLeafOnly:false,
        tree: { url: WST.U('admin/articlecats/listQuery2'), checkbox: false, ajaxType: 'post', textFieldName : 'catName',idField: 'catId',parentIDField: 'parentId'},
        onSelected: function (value)
        {
        	$('#catId').val(value);
        }
    });
    $('.l-text-combobox').css('width','202');
}

function loadGrid(){
	grid.set('url',WST.U('admin/articles/pageQuery','key='+$('#key').val()));
}

function toggleIsShow(t,v){
	if(!WST.GRANT.WZGL_02)return;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    	$.post(WST.U('admin/articles/editiIsShow'),{id:v,isShow:t},function(data,textStatus){
			  layer.close(loading);
			  var json = WST.toAdminJson(data);
			  if(json.status=='1'){
			    	WST.msg(json.msg,{icon:1});
		            grid.reload();
			  }else{
			    	WST.msg(json.msg,{icon:2});
			  }
		});
}

function toEdit(id){
	location.href=WST.U('admin/articles/toEdit','id='+id);
}

function toEdits(id){
    var params = WST.getParams('.ipt');
    params.id = id;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/articles/'+((id>0)?"edit":"add")),params,function(data,textStatus){
		  layer.close(loading);
		  var json = WST.toAdminJson(data);
		  if(json.status=='1'){
		    	WST.msg(json.msg,{icon:1});
		        setTimeout(function(){ 
			    	location.href=WST.U('admin/articles/index');
		        },1000);
		  }else{
		        WST.msg(json.msg,{icon:2});
		  }
	});
}

function toDel(id){
	var box = WST.confirm({content:"您确定要删除该文章吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/articles/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg(json.msg,{icon:1});
	           			    	layer.close(box);
	           		            grid.reload();
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}
function toBatchDel(){
	var ids = WST.getChks('.chk');
	if(ids.length==0){
		 WST.msg('请选择要删除的文章',{icon:2});
		 return;
	}
	var box = WST.confirm({content:"您确定要删除这些文章吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/articles/delByBatch'),{ids:ids.join(',')},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg(json.msg,{icon:1});
	           			    	layer.close(box);
	           		            grid.reload();
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}