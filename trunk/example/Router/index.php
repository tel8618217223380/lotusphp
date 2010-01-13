﻿<?php
/**
 * 加载Cookie类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Router/Router.php";

//unset($_SERVER["PATH_INFO"]);

$router = new LtRouter;
$router->routingTable = array('pattern' => ":module-:action-*",
	'default' => array('module' => 'default', 'action' => 'index'),
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
	'varprefix' => ':',
	'delimiter' => '-',
	'postfix' => '.html',
	'protocol' => 'PATH_INFO', // REWRITE STANDARD
	);
$router->init();

echo "<pre>\n";
echo "输入测试url例如 m-a-id-123.html\n";
print_r($router);
echo $router->url('news','list',array('catid'=>20,'page'=>10)) . "\n";
echo http_build_query($router->params) . "\n";
echo "\n</pre>\n";
