<html>
<head>
	<title>{$data.title}Using View</title>
	<style type="text/css">
/* Global CSS */

<!-- 
	* {
		font-size: 96%;
		font-family: verdana;
	} 
-->
	/*
	ע�����ݱ�ģ������ɾ��
	ģ��������css{}��Ҫ�пո�,��ֹ��������
	*/
<!--[if IE]>
body{ margin:0;padding:0; }
<![endif]-->

	</style>
<script type="text/javascript">
// ע�����ݱ�ģ������ɾ��
//<![CDATA[
var logged_in_user_email = null;
//]]>

	/*
	ע�����ݱ�ģ������ɾ��
	*/
<!--//<![CDATA[
 var codesite_token = null;
//]]>-->
</script>

</head>
<body>
<!--[if IE 6]>
<div style="text-align:center;">
Support browsers that contribute to open source, try <a href="http://www.firefox.com">Firefox</a> or <a href="http://www.google.com/chrome">Google Chrome</a>.
</div>
<![endif]-->
<hr />
Navigator:
<a href="simplest_tpl.php?module=User&action=Signin">User Signin</a>
<a href="simplest_tpl.php?module=test&action=UsingComponent">Using Component</a>
<a href="simplest_tpl.php?module=test&action=UsingBlankLayout">Using Blank Layout</a>
<a href="simplest_tpl.php?module=test&action=PassData">Pass Data from Action</a>
<a href="simplest_tpl.php?module=test&action=UsingTitle">Using Title</a>

<pre>
	Action file: {$this->context->uri['module']}{$this->context->uri['action']}Action.php
	Layout file: {__FILE__}{LF}
	Template file: {$this->templateDir}{$this->template}.php
</pre>
<hr />
{include $this->templateDir . $this->template . '.php'}
<hr />

<!-- 
���� ����123֮��, ��ĸabcDEF֮�࣬ע�����ݱ�ɾ����	
	 -->

<!-- <pre>.test delete  -> [??] !</pre> -->
<pre>
// ����ע��
/*
����
ע��
�п����ǳ�����ʾ����
����
*/
<!--  -->
</pre>

<script type="text/javascript">
// ע�����ݱ�ģ������ɾ��
//<![CDATA[
var logged_in_user_email = null;
//]]>

	/*
	ע�����ݱ�ģ������ɾ��
	*/
<!--//<![CDATA[
 var codesite_token = null;
//]]>-->
</script>

</body>
</html>