var userPic;
var falg = true;
$(function () {
  $('#tab').TabPanel({tab:0,callback:function(no){
    if(no==1 && falg){
      uploadUserPhoto();falg = false;
    }
    
  }});
});


function checkCoords()
{
  //判断是否有裁剪
  if (parseInt($('#w').val())){
    var loading = WST.msg('图像处理中，请稍候...', {icon: 16,shade: [0.3, '#999999']});
    /*获取裁剪数据*/
    var photoData = {};
    photoData.x = $('#x').val();
    photoData.y = $('#y').val();
    photoData.w = $('#w').val();
    photoData.h = $('#h').val();
    photoData.photoSrc = $('#photoSrc').val(); 
    $.post(WST.U('Home/users/editUserPhoto'),photoData,function(data, textStatus){
      if(data.status==1)
      {
        layer.close(loading);
        //将上传的图片路径赋给全局变量
            userPic = data.data;
            $('#userPhotoPreview').html('<img id="userPhoto" class="ipt" src="'+WST.conf.ROOT+'/'+userPic+'?='+Math.random()+'"  height="150" />');
            $('#userPhotoPreview1').html('<img id="userPhoto1" class="ipt" src="'+WST.conf.ROOT+'/'+userPic+'?='+Math.random()+'"  height="150" />');
              $('#userPhotoCut').hide();
              $('#userPhoto').show();
      }else{
        WST.msg(data.msg,{icon:2});
        return false;
      }
    });
    return true;
  }
  WST.msg('请对图片裁剪后再进行提交',{icon:2});
  return false;
}

function uploadUserPhoto()
{
  WST.upload({
    pick:'#userPhotoPicker',
    formData: {dir:'users',isCut:1},
    accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
    callback:function(f){
      var json = WST.toJson(f);
      if(json.status==1){
        /*上传成功*/
        $('#userPhotoCut').show();
        $('#userPhoto').hide();
        
        var uploadPhotoSrc = WST.conf.ROOT+"/"+json.savePath+json.thumb+'?='+Math.random();

        var cutCode = '<img src="'+uploadPhotoSrc+'" id="target" alt="" style="max-width:606;height:auto;" />';
        cutCode += '<div id="preview-pane">';
        cutCode += '<div><p align="center">裁剪预览</p>';
        cutCode += '<div class="cut-help"><h4>操作帮助</h4>'
        cutCode += '<p>请在裁剪区域放大缩小及移动选取框，选择要裁剪的范围，裁切宽高比例固定；裁切后的效果为右侧预览图所显示；保存提交后生效。</p></div></div>'
        cutCode += '<div class="preview-container"><img src="'+uploadPhotoSrc+'"  class="jcrop-preview"  alt="Preview" /></div></div>';
        $('#userPhotoCutBox>p').html(cutCode);
        $('#photoSrc').val(json.savePath+json.thumb);
        $('#userPic').val(json.savePath+json.thumb);
        //初始化jcrop
        jcropInit();
      $('#uploadMsg').empty().hide();
      
      }else{
        WST.msg(json.msg,{icon:2});
      }
      },
      progress:function(rate){
          $('#uploadMsg').show().html('已上传'+rate+"%");
      }
      });
}

var jcrop_api;
function jcropInit(){
    var boundx,
        boundy,

        $preview = $('#preview-pane'),
        $pcnt = $('#preview-pane .preview-container'),
        $pimg = $('#preview-pane .preview-container img'),

        xsize = $pcnt.width(),
        ysize = $pcnt.height();

    $('#target').Jcrop({
      onChange: updatePreview,
      onSelect: updatePreview,
      aspectRatio: 1,
    },function(){
      // Use the API to get the real image size
      var bounds = this.getBounds();
      boundx = bounds[0];
      boundy = bounds[1];
      //设置宽度以使文字居中
      $('#img-src').css('width',boundx+'px');

      // Store the API in the jcrop_api variable
      jcrop_api = this;
      jcrop_api.setSelect([0,0,150,150]);

      // Move the preview into the jcrop container for css positioning
      $preview.appendTo(jcrop_api.ui.holder);
    });

    function updatePreview(c)
    {
      if (parseInt(c.w) > 0)
      {
        var rx = xsize / c.w;
        var ry = ysize / c.h;

        $pimg.css({
          width: Math.round(rx * boundx) + 'px',
          height: Math.round(ry * boundy) + 'px',
          marginLeft: '-' + Math.round(rx * c.x) + 'px',
          marginTop: '-' + Math.round(ry * c.y) + 'px'
        });
      }
        //设置裁剪的图片数据
      $('#x').val(c.x);
      $('#y').val(c.y);
      $('#w').val(c.w);
      $('#h').val(c.h);
    };
}


$(function(){
  /*日期插件*/
  laydate({
  elem: '#brithday', //需显示日期的元素选择器
  event: 'click', //触发事件
  format: 'YYYY-MM-DD', //日期格式
  istime: false, //是否开启时间选择
  isclear: true, //是否显示清空
  istoday: true, //是否显示今天
  issure: true,  //是否显示确认
  festival: true, //是否显示节日
  min: '1900-01-01', //最小日期
  max: '2099-12-31', //最大日期
  fixed: false, //是否固定在可视区域
  zIndex: 99999999 //css z-index
  });
  
  /* 表单验证 */
  $('#userEditForm').validator({
          fields: {
              userName: {rule:"required",msg:{required:"请输入昵称"},tip:"请输入昵称"},
              userSex:  {rule:"checked;",msg:{checked:"至少选择一项"},tip:"请选择您的性别"}
          },
        valid: function(form){
          var params = WST.getParams('.ipt');
          if(!userPic){
            userPic = $('#userPic').val();
          }
          //接收上传的头像路径
          params.userPhoto = userPic;
          var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
          $.post(WST.U('Home/Users/toEdit'),params,function(data,textStatus){
            layer.close(loading);
            var json = WST.toJson(data);
            if(json.status=='1'){
                WST.msg("操作成功",{icon:1});
                return;
            }else{
                  WST.msg(json.msg,{icon:2});
            }
          });
  },
    
  });

});