<?php
/**
 * 这个数组变量叫什么都是无所谓的，只要在末尾把它return即可
 */
$config = array();

/**
 * 如果使用多数据库环境, 不要设置db_only_one
 */
$config['db_only_one'] = array();
/**
 * 多库配置
 */
$config['db_server'][] = array("user_group", "user_node_1", "master", array("host" => "10.0.1.1", "password" => "123456", "adapter" => "mysqli", "dbname" => "member_1"));

$config['db_server'][] = array("group_1", "node_1", "master", array("adapter" => "sqlite", "host" => '/tmp/Lotus/unittest/DBSqlite/', "port" => '', "password" => "", "dbname" => 'sqlite_test1.db', 'pconnect' => ''));

$config['db_server'][] = array("group_8", "node_8", "master", array("adapter" => "mysql", "host" => 'localhost', "port" => '', 'username' => 'root', "password" => "123456", "dbname" => 'test'));
/**
 * 路由表配置
 */
$config['router.routing_table'] = array('pattern' => ":module/:action/*",
	'default' => array('module' => 'default', 'action' => 'index'),
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
	'varprefix' => ':',
	'delimiter' => '/',
	'postfix' => '',
	'protocol' => 'path_info', // standard rewrite path_info
	);

/**
 * 一定不要忘了这个return语句
 * 如果不return，include(conf.php)的时候收到的返回值是整数1
 * 加了return，include(conf.php)收到的返回值才是数组 
 * lotusphp需要的返回值是一个数组
 */
return $config;
