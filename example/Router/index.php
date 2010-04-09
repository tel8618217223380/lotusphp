﻿<?php
/**
 * 加载Cookie类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example")).'/';
include $lotusHome . "runtime/Config.php";
include $lotusHome . "runtime/Store.php";
include $lotusHome . "runtime/StoreMemory.php";
include $lotusHome . "runtime/Router/Router.php";

$router = new LtRouter;
$config['router.routing_table'] = array('pattern' => ":module-:action-*",
	'default' => array('module' => 'default', 'action' => 'index'),
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
	'varprefix' => ':',
	'delimiter' => '-',
	'postfix' => '.html',
	'protocol' => 'rewrite', // REWRITE STANDARD PATH_INFO 
	);

$router->configHandle->addConfig($config);
$router->init();

echo "<pre>\n";
echo '<a href="m-a-id-123.html">测试</a><hr />';
print_r($_GET);
echo http_build_query($_GET) . "\n";
echo "\n</pre>\n";
