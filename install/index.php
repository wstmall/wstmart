<?php
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtaosoft.com
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */ 
header("Content-type:text/html;charset=utf-8" );
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
if (version_compare(PHP_VERSION, '5.4', '<')) {
    die("系统需运行在PHP5.4以上版本（注意：PHP5.4dev版本和PHP6均不支持）!!!");
}
define('IN_WSTMART', TRUE);
define('INSTALL_ROOT', dirname(dirname(__FILE__)));
define('INSTALL_PATH', dirname(__FILE__));
require INSTALL_PATH.'/include/install_var.php';
require INSTALL_PATH.'/include/install_function.php';
$step = (int)$_GET['step'];
if($step<3){
	if(file_exists(INSTALL_PATH.'/install.ok'))header("Location:../index.php");;
}
timezone_set();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>WSTMart开源商城安装</title>
<link href="./css/general.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="js/install.js"></script>
<script type="text/javascript">
</script>
</head>
<body>
<div style="width:960px;margin:0 auto;">
    <div style="margin:0 auto;margin-top:20px;background:url('./images/logo.png') no-repeat left top;width:250px;height:70px;"></div>
    <form id='form1'action='index.php'>
    <input type='hidden' name="step" id='step' value='0'/>
    <input type='hidden' name="rnd" id='rnd' value='0'/>
    <?php if($step==0){?>
    
    <div id="system_agreement" class="main">
        <div class='content'>
            <p class='bold center'>WSTMart安装协议</p>
			<p>版权所有(c)2016,广州商淘信息科技有限公司</p>
			<p style='margin-top:10px;'>用户须知：</p>
			<p style='margin-top:10px;text-indent: 2em'>感谢您选择WSTMart电子商务系统（以下简称WSTMart），<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>电子商务系统由广州商淘信息科技有限公司（以下简称本公司）基于ThinkPHP框架开发并发布的多用户电商系统。本协议是您与广州商淘信息科技有限公司之间关于您使用<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>电子商务系统的法律协议。无论您是个人或组织 、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议。</p>
			<p style='margin-top:10px;text-indent: 2em'>使用协议：</p>
			<p style='margin-top:10px;text-indent: 2em'>1. <a target='_blank' href='http://www.wstmart.net'>WSTMart</a>不是一个自由软件！未经本公司授权，您只能在不用于商业目的的前提下对程序代码进行修改和使用；不允许对程序代码以任何形式任何目的的再发布。</p>
			<p style='text-indent: 2em'>2. 未经本公司书面授权许可无论用途如何、是否经过修改或美化、修改程度如何，只要使用本软件的整体或任何部分，软件首页顶部及页脚处的版权标识（Powered by <a target='_blank' href='http://www.wstmart.net'>WSTMart</a>）和本公司下属网站（<a target='_blank' href='http://www.wstmart.net'>http://www.wstmart.net</a>） 的链接都必须保留，不能清除或修改。</p>
			<p style='margin-top:10px;text-indent: 2em'>限制条款：</p>
			<p style='text-indent: 2em'>1. 用户可通过购买《WSTMart商业授权许可》以获得约定的WSTMart电子商务系统的使用授权。</p>
			<p style='text-indent: 2em'>2. 禁止在 <a target='_blank' href='http://www.wstmart.net'>WSTMart</a>的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</p>
			<p style='text-indent: 2em'>3. 授权用户可以根据需要对<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>进行必要的修改和美化，以适应用户的网站要求。<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>持有产品的全部版权。授权用户可以去除WSTMart网站外在的版权信息，去除外在版权信息后的产品的所有版权仍归本公司所有。</p>
            <p style='margin-top:10px;text-indent: 2em'> 免责条款：</p>
            <p style='text-indent: 2em'>1. <a target='_blank' href='http://www.wstmart.net'>WSTMart</a>及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式出售的。</p>
            <p style='text-indent: 2em'>2. 用户同意自己承担使用本产品的风险，在适用法律允许的最大范围内，<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>在任何情况下不就因使用或不能使用本产品所发生的特殊的、意外的、非直接或间接的损失承担赔偿责任。即使用户已事先被<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>告知该损害发生的可能性。</p>
            <p style='text-indent: 2em'>3. 用户利用本产品构建的网站的任何信息内容以及导致的任何版权纠纷和法律争议及后果与<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>无关，<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>对此不承担任何责任。</p>
            <p style='margin-top:10px;text-indent: 2em'> 其他条款：</p>
            <p style='text-indent: 2em'>用户一旦开始安装使用<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，造成违反本协议并构成侵权的，<a target='_blank' href='http://www.wstmart.net'>WSTMart</a>有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</p>
            <div style='margin-top:5px;;margin-bottom:5px;'>---------------------------------------------------------------------------------------------------------------------------------------------------------</div>
            <p style='text-indent: 2em'>联系我们: 广州商淘信息科技有限公司</p>
            <p style='text-indent: 2em'>WSTMart官方网站：http://www.wstmart.net</p>
            <p style='text-indent: 2em'>WSTMart演示站：http://demo.wstmart.net</p>
            <p style='text-indent: 2em'>客服 Q Q：153289970</p>
            <p style='text-indent: 2em'>交流QQ群：590755485</p>
            <p style='text-indent: 2em'>联系电话: 020-85289921/15918671994</p>
            <p style='text-indent: 2em'>联系邮箱: wstmart@qq.com</p>
        </div>
        <div class='bottom'>
        <input type='button' class='btn' value='我同意' onclick='showStep(1)'/>
        </div>
    </div>
    <?php 
    }else if($step==1){
    	$env_items = env_check($env_items);
        $dir_items = dir_check($dir_items);
        $func_items = check_func($func_items);
    ?>
    <div id="system_env" class="main">
        <div class="content">
            <span class='bold' style='font-size:15px;'>系统环境检查</span>
            <table class="check-env" style='margin-bottom:20px;'>
                <?php 
                    echo '<tr><td class="left">操作系统</td><td><span class="check'.$env_items['os']['status'].'"></span>'.$env_items['os']['current'].'</td></tr>';
                    echo '<tr><td class="left">PHP 版本</td><td><span class="check'.$env_items['php']['status'].'"></span>'.$env_items['php']['current'].'</td></tr>';
                    echo '<tr><td class="left">附件上传</td><td><span class="check'.$env_items['attachmentupload']['status'].'"></span>'.$env_items['attachmentupload']['current'].'</td></tr>';
                    echo '<tr><td class="left">GD 库</td><td><span class="check'.$env_items['gdversion']['status'].'"></span>'.$env_items['gdversion']['current'].'</td></tr>';
                    echo '<tr><td class="left">磁盘空间 </td><td><span class="check'.$env_items['diskspace']['status'].'"></span>'.$env_items['diskspace']['current'].'</td></tr>';
                ?>
            </table>
            <span class='bold' style='font-size:15px;'>目录权限检查</span>
            <table class="check-env" style='margin-bottom:20px;'>
                <?php 
                    $str = '';
                    if(count($dir_items)>0){
                        $check = true;
                        foreach($dir_items as $v){
                            $str .= '<tr><td class="left">'.$v['path'].'</td><td>';
                            if($v['status'] == 1) {
                                $str .= '<span class="check1"></span>可写';
                            }else if($v['status'] == -1) {
                                $str .= '<span class="check-1"></span>目录不可写，请检查后再试';
                                $check = false;
                            }else {
                                $str .= '<span class="check-1"></span>不可写，请检查后再试';
                                $check = false;
                            }
                            $str .= '</td></tr>';
                        }
                    }
                    echo $str;
                ?>
            </table>
            <span style="display:none;color:red;margin-top:30px;text-align:center;" id="envInfo">目录不存在或不可写，请检查后再试</span>
            <span class='bold' style='font-size:15px;'>依赖函数检查</span>
            <table class="check-env">
                <?php 
                    echo '<tr><td class="left">mysqli_connect()</td><td><span class="check'.$func_items['mysqli_connect']['status'].'"></span>'.$func_items['mysqli_connect']['current'].'</td></tr>';
                    echo '<tr><td class="left">file_get_contents()</td><td><span class="check'.$func_items['file_get_contents']['status'].'"></span>'.$func_items['file_get_contents']['current'].'</td></tr>';
                    echo '<tr><td class="left">curl_init()</td><td><span class="check'.$func_items['curl_init']['status'].'"></span>'.$func_items['curl_init']['current'].'</td></tr>';
                    echo '<tr><td class="left">mb_strlen()</td><td><span class="check'.$func_items['mb_strlen']['status'].'"></span>'.$func_items['mb_strlen']['current'].'</td></tr>';
                    echo '<tr><td class="left">php_fileinfo</td><td><span class="check'.$func_items['finfo_open']['status'].'"></span>'.$func_items['finfo_open']['current'].'</td></tr>';
                ?>
            </table>
        </div>
        <div class='bottom'>
        <input type='button' class='btn' value='重新检测' onclick='javascript:showStep(1,1)'/>
        <input type='button' class='btn nextBtn' value='下一步' onclick='showStep(2)'/>
        </div>
    </div>
    <?php }else if($step==2){?>
    <div id="system_data" class='main'>
       <div class='content'>
          <div id='data_config'> 
             <span class='bold' style='font-size:15px;'>数据库帐号</span>
             <table class='check-env'>
                <tbody>
                <tr>
                  <td width="130" align="right" class="item">数据库主机<span class='red'>*</span>：</td>
                  <td align="left">
                      <input type="text" class="ipt" name="db_host" id="db_host" value="127.0.0.1" onblur='checkVal(this.id)'>
                      <span class='db_host tips'>数据库主机不能为空</span>
                  </td>
                </tr>
                <tr>
                  <td width="130" align="right" class="item">数据库端口<span class='red'>*</span>：</td>
                  <td align="left">
                      <input type="text" class="ipt" name="db_port" id="db_port" value="3306" onblur='checkVal(this.id)'>
                      <span class='db_port tips'>数据库端口不能为空</span>
                  </td>
                </tr>
                <tr>
                  <td align="right">访问账号<span class='red'>*</span>：</td>
                  <td align="left">
                      <input type="text" class="ipt" name="db_user" id="db_user" value="root" onblur='checkVal(this.id)'>
                      <span class='db_user tips'>数据库访问账号不能为空</span>
                  </td>
                </tr>
                <tr>
                  <td align="right">访问密码：</td>
                  <td align="left">
                      <input type="password" class="ipt" name="db_pass" id="db_pass" value="">
                  </td>
                </tr>
                <tr>
                   <td align="right">数据库名<span class='red'>*</span>：</td>
                   <td align="left">
                      <input type="text" class="ipt" name="db_name" id="db_name" value="wstmart" onblur='checkVal(this.id)'>
                      <span class='db_name tips'>数据库名不能为空</span>
                      <span class="tips" style='display:inline-block;color: red;'>&nbsp; (若数据库存在则会覆盖原数据库，不存在则会创建一个新数据库)</span>
                   </td>
                </tr>
                <tr>
                   <td align="right">表前缀：</td>
                   <td align="left">
                      <input type="text" class="ipt" name="db_prefix" id="db_prefix" value="wst_" onblur='checkVal(this.id)'>
                      <span class="tips" style='display:inline-block;color: red;'>&nbsp; (建议修改表前缀)</span>
                   </td>
                </tr>
                <tr>
                   <td align="right">&nbsp;</td>
                   <td align="left">
                      <label>
                      <input type="checkbox" name="db_demo" id="db_demo" checked><span class="tips" style='display:inline;color: #121212;'>&nbsp;安装演示数据</span>
                      <span class="tips">&nbsp; </span>
                      </label>
                   </td>
                </tr>
           </tbody>
          </table>
          <span class='bold' style='font-size:15px;'>管理员帐号</span>
          <table class='check-env'>
          <tbody>
              <tr>
                <td width="130" align="right">管理员账号<span class='red'>*</span>：</td>
                <td align="left">
                   <input type="text" class="ipt" name="admin_name" id="admin_name" value="admin" onblur='checkVal(this.id)'>
                   <span class='admin_name tips'>管理员账号不能为空</span>
                </td>
              </tr>
              <tr>
                 <td align="right">登录密码<span class='red'>*</span>：</td>
                 <td align="left">
                   <input type="password" class="ipt" name="admin_password" id="admin_password" value="" onblur='checkVal(this.id)'>
                   <span class='admin_password tips'>管理员密码不能为空</span>
                 </td>
               </tr>
               <tr>
                  <td align="right">密码确认<span class='red'>*</span>：</td>
                  <td align="left">
                    <input type="password" class="ipt" name="admin_password2" id="admin_password2" value="" onblur='checkVal(this.id)'>
                    <span class="admin_password2 tips">两次输入的密码不一致</span>
                  </td>
               </tr>
            </tbody>
           </table>
           </div>
           <div id='data_init' style='display:none'></div>
        </div>
        
        <div class='bottom'>
           <span id='init_msg' style='display:none'><img width='16' src='images/loading-2.gif'>正在初始化数据库...</span>
           <input type='button' class='btn' value='上一步' onclick='showStep(1)'/>
		   <input type='button' class='btn nextBtn' value='下一步' onclick='showStep(3)'/>
	    </div>
    </div>
    <?php }else if($step==3){?>
    <div class="main" id="system_success">
        <div class="content" style='text-align:center;'>
        <div style="margin-top: 120px;">
        <div style="margin:0 auto;background:url('./images/icon_success.png') no-repeat left top;width:62px;height:62px;"></div>
        <span style="display:inline-block;margin-top:10px;">
           <span style="font-size:18px;color: red;"> 恭喜，WSTMart已安装成功&nbsp;!</span><br /><br />
            安装成功后，建议删除install目录
        </span><br /><br />
        <div style="margin:0 auto;width:350px;">
        <a class="btn2" href="../index.php" target="_blank" title="跳到WSTMArt首页"><span>跳到WSTMart首页 </span></a>&nbsp;&nbsp;
        <a class="btn2" href="../index.php/Admin/index" target="_blank" title="跳到WSTMArt后台"><span>跳到WSTMart后台 </span></a>
        </div>
        </div>
        </div>
    </div>
    <?php }?>
    </form>
    </div>
</body>
</html>