<html>
<head>
	<title>{$data.title}Using View</title>
	<style type="text/css">
	* {
		font-size: 96%;
		font-family: verdana;
	}
	</style>
</head>
<body>
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
<pre>
<?php print_r(get_included_files());?>
</pre>
</body>
</html>