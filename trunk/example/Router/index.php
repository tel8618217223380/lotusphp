﻿<?php
/**
 * 加载基本类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example")).'/';
include $lotusHome . "runtime/Config.php";
include $lotusHome . "runtime/Store.php";
include $lotusHome . "runtime/StoreMemory.php";
include $lotusHome . "runtime/Router/Router.php";

$router = new LtRouter;
$config['router.routing_table'] = array(
	// URL中的变量名字
	'pattern' => ":module-:action-*",
	// 默认的module和action的名字
	'default' => array('module' => 'default', 'action' => 'index'),
	// 对URL中的变量名字进行正则匹配，满足条件才注册此变量
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 
				'action' => '[a-zA-Z0-9\.\-_]+'),
	// 标识变量的前缀，对应URL中变量名字
	'varprefix' => ':',
	// URL中变量的分隔符号
	'delimiter' => '-',
	// 后缀，常用来将URL模拟成单个文件
	'postfix' => '.htm',
	// REWRITE STANDARD PATH_INFO三种模式，不分大小写
	'protocol' => 'REWRITE',  
	);
// 加载配置
$router->configHandle->addConfig($config);
// 调用init
$router->init();


echo "<pre>\n";
echo '<a href="m-a-id-123.html">测试REWRITE模式</a><hr />';

// 注册好的变量放入$_GET，没有通过正则匹配的变量会删除
print_r($_GET);

echo http_build_query($_GET) . "\n";
echo "\n</pre>\n";