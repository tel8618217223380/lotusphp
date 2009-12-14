<?php
/**
 * 这是一个最简单的示例，没有配置文件，没有MVC
 * 适合用来开发服务器上定时运行的脚本，如数据迁移的脚本
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Lotus.php";

/**
 * 初始化Lotus类
 */
$lotus = new Lotus();
$lotus->option = array(
	"cache_adapter" => "apc",
);

/**
 * envMode的默认值是dev，即开发模式
 * envMode不等于dev的时候（如prod-生产环境，testing-测试环境），性能会有提高
 * $lotus->envMode = "prod";
 */
$lotus->boot();

/**
 * 配置数据库连接
 * 关键是给Db::$servers变量赋一个数组，这个数组维度比较复杂 ，所以用DbConfigBuilder构建不容易出错
 * 如果你用别的方式（例如从ini或者yaml读取配置）构造一个同样的数组然后赋值给Db::$servers，效果是一样的
 */
$dbConfigBuilder = new LtDbConfigBuilder();
$dbConfigBuilder->addSingleHost(array(
	"host" => "localhost",
	"username" => "root",
	"password" => "123456",
	"dbname" => "lotus_db_test",
	"adapter" => "pdoMysql",
	"charset" => "UTF-8",
));
LtDb::$servers = $dbConfigBuilder->getServers();

/**
 * 直接执行执行SQL
 * 由于PDO::execute()的潜规则，这里三个查询只能分三次执行，不要合并成这样：$dba->query("$sql1; $sql2; $sql3");
 */
$dba = LtDb::factory("pdoMysql");
$dba->query("CREATE DATABASE IF NOT EXISTS lotus_db_test;");
$dba->query("USE lotus_db_test;");
$dba->query("
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

/**
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