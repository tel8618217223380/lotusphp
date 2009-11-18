<?php
/*
 * 加载Db类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/DB/Db.php";
include $lotusHome . "/runtime/DB/DbConfig.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapter.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapterPdo.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapterPdoMysql.php";

/*
 * 配置数据库连接
 */
$dbConfig = new DbConfig();
$dbConfig->addSingleHost(array(
	"host" => "localhost",
	"username" => "root",
	"password" => "123456",
	"dbname" => "mysql",
	"adapter" => "pdoMysql",
	"charset" => "UTF-8",
));
Db::$servers = $dbConfig->getServers();

/*
 * 执行SQL查询
 */
$dba = Db::factory("pdoMysql");
print_r($dba->query("SELECT NOW()"));
