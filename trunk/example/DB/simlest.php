<?php
/*
 * 加载Db类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/DB/DbConfigBuilder.php";
include $lotusHome . "/runtime/DB/Db.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapter.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapterPdo.php";
include $lotusHome . "/runtime/DB/Adapter/DbAdapterPdoMysql.php";
include $lotusHome . "/runtime/DB/QueryEngine/DbTable.php";

/*
 * 配置数据库连接
 * 关键是给Db::$servers变量赋一个数组，这个数组维度比较复杂 ，所以用DbConfigBuilder构建不容易出错
 * 如果你用别的方式（例如从ini或者yaml读取配置）构造一个同样的数组然后赋值给Db::$servers，效果是一样的
 */
$dbConfigBuilder = new DbConfigBuilder();
$dbConfigBuilder->addSingleHost(array(
	"host" => "localhost",
	"username" => "root",
	"password" => "123456",
	"dbname" => "lotus_db_test",
	"adapter" => "pdoMysql",
	"charset" => "UTF-8",
));
Db::$servers = $dbConfigBuilder->getServers();

/*
 * 直接执行执行SQL
 */
$dba = Db::factory("pdoMysql");
$result = $dba->query("CREATE DATABASE IF NOT EXISTS lotus_db_test;");
$result = $dba->query("USE lotus_db_test;");
$result = $dba->query("
CREATE TABLE IF NOT EXISTS `user` (
	`user_id` INT NOT NULL AUTO_INCREMENT COMMENT '用户ID',
	`username` VARCHAR( 20 ) NOT NULL COMMENT '用户名',
	`age` INT NOT NULL COMMENT '年龄',
	`created` INT NOT NULL COMMENT '账号创建时间',
	`modified` INT NOT NULL COMMENT '最后修改时间',
	PRIMARY KEY ( `user_id` ) ,
	UNIQUE (
	`username`
	)
);
");
print_r($result);

/*
 * 配置数据表，和“配置数据库连接”一样，用别的方式构造一个数组并赋值给Db::$tables也可以
 */
Db::$tables = $dbConfigBuilder->getTables();

/*
 * 使用Table Gateway模式操作数据表
 * 使用上面刚刚建立的test库，user表
 */
$userTDG = Db::newDbTable("user");

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

//根据主键更新
$userTDG->update($userId, array(
	"age" => 31
));

//更新多条
$userTDG->updateRows($condition, array(
	"age" => 9
));

//根据主键删除
$userTDG->delete($userId);

//删除多条
$userTDG->deleteRows($condition);
