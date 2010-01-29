<html>
<head>
	<title><!--{if isset($this->data['title'])}-->{$this->data['title']}<!--{/if}-->Using View</title>
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
<a href="simplest_tpl.php?module=test&action=UsingComponent">Using Component</a>
<a href="simplest_tpl.php?module=test&action=UsingBlankLayout">Using Blank Layout</a>
<a href="simplest_tpl.php?module=test&action=PassData">Pass Data from Action</a>
<a href="simplest_tpl.php?module=test&action=UsingTitle">Using Title</a>
<xmp>
<?php
echo "Action file: " . $this->context->uri['module'] . $this->context->uri['action'] . "Action.php\n";
echo "Layout file: " . __FILE__ . "\n";
echo "Template file: " . $this->templateDir . $this->template . ".php\n";
?>
</xmp>
<hr />
{include $this->templateDir . $this->template . '.php'}
</body>
</html>