<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>��½ϵͳ</title>
<link href="{$this->data['baseurl']}css/default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="top"><span style="float:right;margin-right:24px;"><a href="/">������ҳ</a></span></div>
<div id="loginbox">
  <form  action="{url('User','DoLogin')}" method="post" name="myform" id="myform">
    <h3>��½ϵͳ</h3>
    <table border="0" cellpadding="0" cellspacing="0" summary="��½">
      <tbody>
        <tr>
          <td>�ʡ���:</td>
          <td><input name="username" type="text" id="username" size="20"/></td>
        </tr>
        <tr>
          <td>�ܡ���:</td>
          <td><input name="password" type="password" id="password" size="21"/></td>
        </tr>
<!--
        <tr>
          <td></td>
          <td><img align="absmiddle" alt="��֤��,�������?����ˢ����֤��" style="cursor: pointer;" onclick="this.src='{url('Captcha','Image',array('seed'=>123456))}&tmp='+Math.random();" id="checkcode" src="{url('Captcha','Image',array('seed'=>123456))}"></td>
        </tr>
        <tr>
          <td></td>
          <td><a href="javascript:showck();">�����壬��һ�š�</a></td>
        </tr>
        <tr>
          <td>��֤��:</td>
          <td><input name="checkcode" type="text" id="checkcode" size="21"/></td>
        </tr>
-->
        <tr>
          <td></td>
          <td><input type="submit" name="dosubmit" id="login" value="�� ½"/></td>
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
//    ck.src = '{url('Captcha','Image',array('seed'=>123456))}&tmp='+ Math.random();//ÿ�β�����ͬ��url����ˢ��
//}
//]]>
</script>
</body>
</html>
