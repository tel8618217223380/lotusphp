<?php

/**
 * 
 * @example :
 * $config['db_only_one'] = array(
 * "adapter" => "sqlite", 
 * "host" => '/tmp/Lotus/DBSqlite/', 
 * "port" => '', 
 * "password" => "", 
 * "dbname" => 'sqlite_test0.db'
 * );
 */
$config['db_only_one'] = array();

$config['db_server'][] = array("user_group", "user_node_1", "master", array("host" => "10.0.1.1", "password" => "123456", "adapter" => "mysqli", "dbname" => "member_1"));

$config['db_server'][] = array("group_1", "node_0", "master", array("adapter" => "sqlite", "host" => '/tmp/Lotus/DBSqlite/', "port" => '', "password" => "", "dbname" => 'sqlite_test0.db', 'pconnect' => ''));

$config['db_server'][] = array("group_1", "node_1", "master", array("adapter" => "pdo_sqlite", "host" => '/tmp/Lotus/DBSqlite/', "port" => '', "password" => "", "dbname" => 'sqlite_test1.db', 'pconnect' => '', 'sqlite_version'=>'sqlite2'));

$config['db_server'][] = array("group_8", "node_8", "master", array("adapter" => "mysql", "host" => 'localhost', "port" => '', 'username' => 'root', "password" => "123456", "dbname" => 'test'));
