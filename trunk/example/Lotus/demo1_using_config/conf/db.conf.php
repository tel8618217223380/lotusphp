<?php
/**
 * 这是DB组件的配置
 * 在配置文件里，可以引用Lotus组件 里的类（比如DbConfigBuilder, ConfigExpression）
 */
$dbConfigBuilder = new LtDbConfigBuilder();
$dbConfigBuilder->addSingleHost(array(
	"host" => "localhost",
	"username" => "root",
	"password" => "123456",
	"dbname" => "test",
	"adapter" => "pdoMysql",
	"charset" => "UTF-8",
));
$config["DB"]["servers"] = $dbConfigBuilder->getServers();