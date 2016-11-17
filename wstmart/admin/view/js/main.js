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
$(function(){
    getLastVersion();
})