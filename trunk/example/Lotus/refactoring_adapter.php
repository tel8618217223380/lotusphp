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
$adapter = "mysql";
$dbConfigBuilder->addSingleHost(array(
	"host" => "localhost",
	"port" => "3306",
	"username" => "root",
	"password" => "123456",
	"dbname" => "test",
	"adapter" => $adapter,
	//"adapter" => "pdo_mysql",//使用pdo_mysql扩展,目前只支持mysql和pdo_mysql,都能运行成功
	"charset" => "UTF-8",
));
LtDbStaticData::$servers = $dbConfigBuilder->getServers();

/**
 * 直接执行执行SQL
 * 由于mysql_query()的潜规则,每次只能执行一条SQL
 */
$dba = new LtDbHandler();
$username = $adapter . time();

echo "\nUSE, DROP, CREATE应该返回true（执行成功）或者false（执行失败）：\n";
var_dump($dba->query("USE shycat"));//故意使用另一个database（和配置里写的不一样），测试各驱动use db好不好用
var_dump($dba->query("DROP TABLE IF EXISTS user;"));
var_dump($dba->query("CREATE TABLE `user` (
	`user_id` INT NOT NULL AUTO_INCREMENT COMMENT '用户ID',
	`username` VARCHAR( 20 ) NOT NULL COMMENT '用户名',
	`age` INT NOT NULL COMMENT '年龄',
	`created` INT NOT NULL COMMENT '账号创建时间',
	`modified` INT NOT NULL COMMENT '最后修改时间',
	PRIMARY KEY ( `user_id` ) ,
	UNIQUE (
	`username`
	)
);"));

echo "\nINSERT应该返回自增ID：\n";
var_dump($dba->query("INSERT INTO user (username, age) VALUES ('$username', '4');"));

echo "\nSELECT应该返回查到的结果集：\n";
var_dump($dba->query("SELECT * FROM user"));

echo "\nUPDATE,DELETE应该返回受影响的行数：\n";
var_dump($dba->query("UPDATE user SET age = 10"));
var_dump($dba->query("DELETE FROM user"));

echo "\nSELECT查不到结果应该返回null：\n";
var_dump($dba->query("SELECT * FROM user"));
