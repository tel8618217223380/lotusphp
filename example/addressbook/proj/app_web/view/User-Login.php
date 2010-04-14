<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>登陆系统</title>
<link href="{$this->data['baseurl']}css/default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="top"><span style="float:right;margin-right:24px;"><a href="/">返回首页</a></span></div>
<div id="loginbox">
  <form  action="{url('User','DoLogin')}" method="post" name="myform" id="myform">
    <h3>登陆系统</h3>
    <table border="0" cellpadding="0" cellspacing="0" summary="登陆">
      <tbody>
        <tr>
          <td>帐　号:</td>
          <td><input name="username" type="text" id="username" size="20"/></td>
        </tr>
        <tr>
          <td>密　码:</td>
          <td><input name="password" type="password" id="password" size="21"/></td>
        </tr>
<!--
        <tr>
          <td></td>
          <td><img align="absmiddle" alt="验证码,看不清楚?请点击刷新验证码" style="cursor: pointer;" onclick="this.src='{url('Captcha','Image',array('seed'=>123456))}&tmp='+Math.random();" id="checkcode" src="{url('Captcha','Image',array('seed'=>123456))}"></td>
        </tr>
        <tr>
          <td></td>
          <td><a href="javascript:showck();">看不清，换一张。</a></td>
        </tr>
        <tr>
          <td>验证码:</td>
          <td><input name="checkcode" type="text" id="checkcode" size="21"/></td>
        </tr>
-->
        <tr>
          <td></td>
          <td><input type="submit" name="dosubmit" id="login" value="登 陆"/></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<div id="footer">&copy; 2010 <span id="debug_info"></span></div>
<script type="text/javascript">
//<![CDATA[
//document.getElementById("username").focus();
//function showck(){
//    var ck = document.getElementById('checkcode');
//    ck.src = '{url('Captcha','Image',array('seed'=>123456))}&tmp='+ Math.random();//每次产生不同的url才能刷新
//}
//]]>
</script>
</body>
</html>
