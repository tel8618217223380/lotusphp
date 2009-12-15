<?php
/*
 * 加载Db类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/DB/DbConfigBuilder.php";
include $lotusHome . "/runtime/DB/Db.php";
include $lotusHome . "/runtime/DB/DbStaticData.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapter.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapterPdo.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapterPdoSqlite.php";
include $lotusHome . "/runtime/DB/QueryEngine/DbTable.php";

/*
 * 配置数据库连接
 * 关键是给Db::$servers变量赋一个数组，这个数组维度比较复杂 ，所以用DbConfigBuilder构建不容易出错
 * 如果你用别的方式（例如从ini或者yaml读取配置）构造一个同样的数组然后赋值给Db::$servers，效果是一样的
 */
$dbConfigBuilder = new LtDbConfigBuilder();
$dbConfigBuilder->addSingleHost(array(
	// host设置Sqlite文件存放目录   
	"host" => dirname(__FILE__).DIRECTORY_SEPARATOR,
	// "dbver" => 'sqlite2', // 2.x 不支持 IF NOT EXISTS 不支持 AUTOINCREMENT
	// dbname设置Sqlite文件名
	"dbname" => "lotus_db_test.db",
	"adapter" => "pdoSqlite",
	"charset" => "UTF-8",
));
LtDbStaticData::$servers = $dbConfigBuilder->getServers();

/*
 * 直接执行执行SQL
 */
$dba = LtDb::factory("pdoSqlite");
$result = $dba->query("
CREATE TABLE IF NOT EXISTS [user] (
	[user_id] INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
	[username] VARCHAR(20)  NOT NULL,
	[age] INTEGER  NOT NULL,
	[created] INTEGER  NOT NULL,
	[modified] INTEGER  NOT NULL
)
");
$result = $dba->query("
	CREATE UNIQUE INDEX IF NOT EXISTS [username] ON [user]([username]  ASC)
");
echo '<pre>';
print_r($result);

/*
 * 使用Table Gateway模式操作数据表
 * 使用上面刚刚建立的lotus_db_test库，user表
 */
$userTDG = LtDb::newDbTable("user");

//插入新记录，并取得自增的ID
$userId = $userTDG->insert(array(
	"username" => "chin",
	"age" => "30",
));

//根据主键查询
print_r($userTDG->fetch($userId));

//再来插入几条
$userTDG->insert(array(
	"username" => "kiwiphp",
	"age" => "3",
));
$userTDG->insert(array(
	"username" => "lotus",
	"age" => "1",
));

//查询多条记录
$condition["where"]["expression"] = "age < 10";
$condition["fields"] = "user_id, username";
print_r($userTDG->fetchRows($condition));
echo '</pre>';
//根据主键更新
$userTDG->update($userId, array(
	"age" => 31
));

//更新多条
$userTDG->updateRows($condition["where"], array(
	"age" => 9
));

//根据主键删除
$userTDG->delete($userId);

//删除多条
$userTDG->deleteRows($condition["where"]);
