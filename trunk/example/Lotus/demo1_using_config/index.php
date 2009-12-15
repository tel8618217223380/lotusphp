<?php
/**
 * 这个示例演示了如何使用配置文件，没有MVC，不需要Web服务器
 * 与Lotus/simplest.php最大的不同只在于把DB的链接信息放到配置文件中去了
 */
$lotusHome = dirname(dirname(dirname(dirname(__FILE__))));
include $lotusHome . "/runtime/Lotus.php";

/**
 * 初始化Lotus类
 */
$lotus = new Lotus();

/**
 * 配置Lotus选项，使之可以使用配置文件
 */
$lotus->option["config_file"] = "conf.php";

/**
 * envMode的默认值是dev，即开发模式
 * envMode不等于dev的时候（如prod-生产环境，testing-测试环境），性能会有提高
 * $lotus->envMode = "prod";
 */
$lotus->boot();

/**
 * 直接使用配置数组中的值
 * C()函数是一个快捷方式，定义在runtime/shortcut.php里，大写的C代表Component的意思
 * C("LtConfig") 等价于  LtObjectUtil::singleton("LtConfig")
 */
print_r(C("LtConfig")->app["Validator"]);

/**
 * ========== 以下内容取自example/DB/simplest.php 演示了如何操作数据库 ==========
 */
/**
 * 直接执行执行SQL
 * 由于PDO::execute()的潜规则，这里三个查询只能分两次执行，不要合并成这样：$dba->query("$sql1; $sql2;");
 */
$dba = LtDb::factory("pdoMysql");
$dba->query("DROP TABLE IF EXISTS user;");
$dba->query("
CREATE TABLE `user` (
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