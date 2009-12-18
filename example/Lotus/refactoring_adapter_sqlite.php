<?php
/**
 * 这是一个最简单的示例，没有配置文件，没有MVC，不需要Web服务器
 * 适合用来开发服务器上定时运行的脚本，如数据迁移的脚本
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Lotus.php";

/**
 * 初始化Lotus类
 */
$lotus = new Lotus();

/**
 * envMode的默认值是dev，即开发模式
 * envMode不等于dev的时候（如prod-生产环境，testing-测试环境），性能会有提高
 * $lotus->envMode = "prod";
 */
$lotus->boot();

/**
 * ========== 以下内容取自example/DB/simplest.php 演示了如何操作数据库 ==========
 */
/**
 * 配置数据库连接
 * 关键是给Db::$servers变量赋一个数组，这个数组维度比较复杂 ，所以用DbConfigBuilder构建不容易出错
 * 如果你用别的方式（例如从ini或者yaml读取配置）构造一个同样的数组然后赋值给Db::$servers，效果是一样的
 */
$dbConfigBuilder = new LtDbConfigBuilder();
$dbConfigBuilder->addSingleHost(array(
	"host" => dirname(__FILE__),
	"port" => "3306",
	"username" => "root",
	"password" => "123456",
	"dbname" => "test.db",
	"adapter" => "sqlite",
	"charset" => "UTF-8",
));
LtDbStaticData::$servers = $dbConfigBuilder->getServers();

/**
 * 直接执行执行SQL
 * 2.x 不支持 IF NOT EXISTS 不支持 AUTOINCREMENT
 */
$dba = new LtDbHandler();


echo "\nDROP, CREATE应该返回true（执行成功）或者false（执行失败）：\n";

var_dump($dba->query("DROP TABLE user"));

var_dump($dba->query("
CREATE TABLE [user] (
	[user_id] INTEGER  NOT NULL PRIMARY KEY,
	[username] VARCHAR(20)  UNIQUE NOT NULL,
	[age] INTEGER  NOT NULL,
	[created] INTEGER  NOT NULL,
	[modified] INTEGER  NOT NULL
)
"));

echo "\nINSERT应该返回自增ID：\n";
$dba->query("BEGIN");
for($i=0; $i< 10; $i++)
{
$time = time()+$i;
$username = "lotus" . $time;
var_dump($dba->query("INSERT INTO user (username, age, created, modified) VALUES ('$username', '4', '$time','$time')"));
}
$dba->query("COMMIT");
echo "\nROLLBACK事务测试\n";
$dba->query("BEGIN");
for($i=11; $i< 21; $i++)
{
$time = time()+$i;
$username = "lotus" . $time;
var_dump($dba->query("INSERT INTO user (username, age, created, modified) VALUES ('$username', '44', '$time','$time')"));
}
$dba->query("ROLLBACK");

echo "\nSELECT应该返回查到的结果集：\n";
var_dump($dba->query("SELECT * FROM user"));

echo "\nUPDATE,DELETE应该返回受影响的行数：\n";
var_dump($dba->query("UPDATE user SET age = 10"));
var_dump($dba->query("DELETE FROM user WHERE user_id in (1,3,5)"));
echo "\nDELETE FROM user返回0，原因未知。DELETE FROM user WHERE 1 加上where结果正常\n";
var_dump($dba->query("DELETE FROM user WHERE 1"));
echo "\nSELECT查不到结果应该返回null：\n";
var_dump($dba->query("SELECT * FROM user"));