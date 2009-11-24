<?php
/*
 * 加载Db类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/DB/DbConfigBuilder.php";

/*
 * 配置分布式数据库连接
 */
$dbConfigBuilder = new DbConfigBuilder();
$dbConfigBuilder->addGroup("group_0");
$dbConfigBuilder->addNode("node_0", "group_0");
$dbConfigBuilder->addHost(array(
	"host" => "10.0.0.1",
	"username" => "root",
	"password" => "123456",
	"dbname" => "lotus_db_test",
	"adapter" => "pdoMysql"
), "master", "node_0", "group_0");

$dbConfigBuilder->addHost(array(
	"host" => "10.0.0.11",
), "slave", "node_0", "group_0");
$dbConfigBuilder->addHost(array(
	"host" => "10.0.0.12",
), "slave", "node_0", "group_0");

/*
 *