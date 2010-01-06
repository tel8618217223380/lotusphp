﻿﻿<?php
/**
 * 加载Cookie类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Router/Router.php";

$router = new LtRouter;
$router->routingTable = array('pattern' => ":module/:action/*",
	'default' => array('module' => 'default', 'action' => 'index'),
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
	'varprefix' => ':',
	'delimiter' => '/'
	);
$router->init();

echo "\n<pre>\n";
print_r($router);
echo $router->url($router->params)."\n";
echo http_build_query($router->params)."\n";
echo "\n</pre>\n";