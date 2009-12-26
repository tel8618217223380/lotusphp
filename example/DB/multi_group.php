<?php
/*
 * 加载Db类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/DB/DbConfigBuilder.php";
include $lotusHome . "/runtime/DB/Db.php";
include $lotusHome . "/runtime/DB/DbStaticData.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapter.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapterPdo.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapterPdoMysql.php";
include $lotusHome . "/runtime/DB/QueryEngine/DbTable.php";

/*
 * 配置数据库连接
 * 关键是给Db::$servers变量赋一个数组，这个数组维度比较复杂 ，所以用DbConfigBuilder构建不容易出错
 * 如果你用别的方式（例如从ini或者yaml读取配置）构造一个同样的数组然后赋值给Db::$servers，效果是一样的
 */
$dbConfigBuilder = new LtDbConfigBuilder();
$dbConfigBuilder->addGroup("lotus_db_test");
$dbConfigBuilder->addGroup("mysql");
$dbConfigBuilder->addHost(array(
	"host" => "localhost",
	"username" => "root",
	"password" => "123456",
	"dbname" => "lotus_db_test",
	"adapter" => "pdoMysql",
	"charset" => "UTF-8",
), null, null,"lotus_db_test");

$dbConfigBuilder->addHost(array(
	"host" => "localhost",
	"username" => "root",
	"password" => "123456",
	"dbname" => "mysql",
	"adapter" => "pdoMysql",
	"charset" => "UTF-8",
), null, null,"mysql");
LtDbStaticData::$servers = $dbConfigBuilder->getServers();

/*
 * 配置表
 */
$dbConfigBuilder->addTable("user", null, null, "lotus_db_test");
$dbConfigBuilder->addTable("host", null, null, "mysql");
LtDbStaticData::$tables = $dbConfigBuilder->getTables();

$userTDG = LtDb::newDbTable("user");
$hostTDG = LtDb::newDbTable("host");

print_r($userTDG->fetchRows());
print_r($hostTDG->fetchRows());