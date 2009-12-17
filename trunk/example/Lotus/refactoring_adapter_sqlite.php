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
	"dbname" => "test",
	"adapter" => "sqlite",
	"charset" => "UTF-8",
));
LtDbStaticData::$servers = $dbConfigBuilder->getServers();

/**
 * 直接执行执行SQL
 */
$dba = new LtDbHandler();
$username = "lotus" . time();
// $dba->query("DROP TABLE IF EXISTS user");
$dba->query("CREATE TABLE [user] (
	[user_id] INTEGER NOT NULL PRIMARY KEY COMMENT '用户ID',
	[username] VARCHAR( 20 ) NOT NULL COMMENT '用户名',
	[age] INTEGER NOT NULL COMMENT '年龄',
	[created] INTEGER NOT NULL COMMENT '账号创建时间',
	[modified] INTEGER NOT NULL COMMENT '最后修改时间',
)");
$dba->query("INSERT INTO user (username, age) VALUES ('$username', '4')");
print_r($dba->query("SELECT * FROM user"));