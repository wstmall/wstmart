$(window).resize(function(){
	var h = WST.pageHeight()-100;
    $('.l-tab-content').height(h);
    $('.l-tab-content-item').height(h);
    $('.wst-iframe').each(function(){
    	$(this).height(h-26);
    });
    $('.wst-accordion').each(function(){
    	liger.get($(this).attr('id')).setHeight(h-26);
    });
});
function changeTab(obj,n){
    var ltab = liger.get("wst-ltabs-"+n);
    ltab.setHeader("wst-ltab-"+n, $(obj).text());
    $('#wst-lframe-'+n).attr('src',$(obj).attr('url'));
}
function initTabMenus(menuId){
	$.post(WST.U('admin/index/getSubMenus'),{id:menuId},function(data,textStatus){
		 var json = WST.toAdminJson(data);
		 var html = [];
		 html.push('<div id="wst-layout-'+menuId+'" style="width:99.2%; margin:0 auto; margin-top:4px; ">'); 
		 html.push('<div position="left" id="wst-accordion-'+menuId+'" title="管理菜单" class="wst-accordion">');
		 if(json && json.length>0){
			 for(var i=0;i<json.length;i++){
       		 html.push('<div title="'+json[i]['menuName']+'">'); 
       		 html.push('     <div style=" height:7px;"></div>');
       		 if(json[i]['list']){
	        		 for(var j=0;j<json[i]['list'].length;j++){
		        		 html.push('<a class="wst-link" href="javascript:void(0)" url="'+WST.U(WST.blank(json[i]['list'][j]['privilegeUrl'],''))+'" onclick="javascript:changeTab(this,'+menuId+')">'+json[i]['list'][j]['menuName']+'</a>');  
	        		 }
       		 }
       		 html.push('     </div> ');
			 }
		 }
		 html.push('</div>');
		 html.push('<div id="wst-ltabs-'+menuId+'" position="center" class="wst-lnavtabs">'); 
		 html.push('  <div tabid="wst-ltab-'+menuId+'" title="我的主页" style="height:300px" >');
		 html.push('      <iframe frameborder="0" class="wst-iframe" id="wst-lframe-'+menuId+'" src="'+(initFrame?"":WST.U('admin/index/main'))+'"></iframe>');
		 html.push('  </div>');
		 html.push('</div>'); 
		 html.push('</div>');
		 initFrame = true;
		 $('#wst-tab-'+menuId).html(html.join(''));
		 $("#wst-layout-"+menuId).ligerLayout({
	         leftWidth: 190,
	         height: '100%',
	         space: 0
	     });
		 var height = $(".l-layout-center").height();
		 $("#wst-accordion-"+menuId).ligerAccordion({
		      height: height - 24, speed: null
		 });
		 $("#wst-ltabs-"+menuId).ligerTab({
		      height: height,
		      changeHeightOnResize:true,
		      showSwitchInTab : false,
		      showSwitch: false
	     });
		 if(initFrame)$('.l-tab-loading').remove();
	 });
}
var mMgrs = {},tab,initFrame = false;
$(function (){   
    tab = $("#wst-tabs").ligerTab({
         height: '100%',
         changeHeightOnResize:true,
         showSwitchInTab : false,
         showSwitch: false,
         onAfterSelectTabItem:function(n){
        	 var menuId = n.replace('wst-tab-','');
        	 if(!mMgrs['m'+menuId]){
	        	 var ltab = $("#wst-tab-"+menuId);
	        	 mMgrs['m'+menuId] = true;
	        	 if(menuId=='market'){
	        		 $('#wst-market').attr('src','http://market.shangtaosoft.com');
	        	 }else{
	        	     initTabMenus(menuId);
        	     }
        	 }
         }
    });
    var tabId = tab.getSelectedTabItemID();
    mMgrs['m'+tabId.replace('wst-tab-','')] = true;
    initTabMenus(tabId.replace('wst-tab-',''));
    $('.l-tab-content').height(WST.pageHeight()-70);
    $('.l-tab-content-item').height(WST.pageHeight()-70);
    $('.wst-iframe').each(function(){
    	$(this).height(h-10);
    });
});
function getLastVersion(){
	$.post(WST.U('admin/index/getVersion'),{},function(data,textStatus){
		var json = {};
		try{
	      if(typeof(data )=="object"){
			  json = data;
	      }else{
			  json = eval("("+data+")");
	      }
		}catch(e){}
	    if(json){
		   if(json.version && json.version!='same'){
			   $('#wstmart-version-tips').show();
			   $('#wstmart_version').html(json.version);
			   $('#wstmart_down').attr('href',json.downloadUrl);
		   }
		   if(json.accredit=='no'){
			   $('#wstmart-accredit-tips').show();
		   }
		   if(json.licenseStatus)$('#licenseStatus').html(json.licenseStatus);
	   }
	});
}
function logout(){
	WST.confirm({content:"您确定要退出该系统吗?",yes:function(){
		var loading = WST.msg('正在退出，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/index/logout'),WST.getParams('.ipt'),function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json.status=='1'){
				location.reload();
			}
		});
	}});
}
function clearCache(){
	var loading = WST.msg('正在清理缓存，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/index/clearcache'),{},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status && json.status=='1'){
			WST.msg(json.msg,{icon:1});
		}else{
			WST.msg(json.msg,{icon:2});
		}
	});
}
function editPassBox(){
	var w = WST.open({type: 1,title:"修改密码",shade: [0.6, '#000'],border: [0],content:$('#editPassBox'),area: ['450px', '250px'],
	    btn: ['确定', '取消'],yes: function(index, layero){
	    	$('#editPassFrom').isValid(function(v){
	    		if(v){
		        	var params = WST.getParams('.ipt');
		        	var ll = WST.msg('数据处理中，请稍候...');
				    $.post(WST.U('admin/Staffs/editMyPass'),params,function(data){
				    	layer.close(ll);
				    	var json = WST.toAdminJson(data);
						if(json.status==1){
							WST.msg(json.msg, {icon: 1});
							layer.close(w);
						}else{
							WST.msg(json.msg, {icon: 2});
						}
				   });
	    		}})
        }
	});
}