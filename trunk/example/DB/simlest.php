<?php
/*
 * 加载Autoloader类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/DB/DbServer.php";

/*
 * 配置数据库连接
 */
$dbServer = new DbServer();
$dbServer->addSingleHost(array(
	"host" => "localhost",
	"username" => "root",
	"password" => "123456",
	"dbname" => "test",
	"adapter" => "pdoMysql"
));

print_r($dbServer->getServers());