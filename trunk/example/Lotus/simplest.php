<?php
/**
 * 这是一个最简单的示例，没有配置文件，没有MVC
 * 适合用来开发服务器上定时运行的脚本，如数据迁移的脚本
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/lotus.php";

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
 * 初始化完毕
 * 所有Lotus组件都已被探测到，下面用到时即可自动加载
 */
$dba = LtDb::factory("pdoMysql");
print_r($dba);