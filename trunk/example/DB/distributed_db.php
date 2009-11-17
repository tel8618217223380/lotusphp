<?php
/*
 * 加载Autoloader类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/DB/DbServer.php";

/*
 * 配置分布式数据库连接
 */
$dbServer = new DbServer();
$dbServer->addGroup("group_0");
$dbServer->addNode("node_0", "group_0");
$dbServer->addHost(array(
	"host" => "10.0.0.1",
	"username" => "root",
	"password" => "123456",
	"dbname" => "test",
	"adapter" => "pdoMysql"
), "master", "node_0", "group_0");

$dbServer->addHost(array(
	"host" => "10.0.0.11",
), "slave", "node_0", "group_0");
$dbServer->addHost(array(
	"host" => "10.0.0.12",
), "slave", "node_0", "group_0");

print_r($dbServer->getServers());