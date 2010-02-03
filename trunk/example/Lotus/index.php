<?php
$lotusHome = substr(dirname(__FILE__), 0, strpos(__FILE__, "example"));
include $lotusHome . 'runtime/Lotus.php';
/**
 * 初始化Lotus类
 */
$lotus = new Lotus();
/**
 * 项目目录
 */
$lotus->option['proj_dir'] = dirname(__FILE__) . '/proj_dir/';
/**
 * 临时目录,默认是proj_dir/tmp/
 * 开发模式下的Autoloader 和 MVC的模板引擎 及 文件类型Cache
 */
$lotus->option['tmp_dir'] = '/tmp/';

/**
 * 应用名称对项目目录下的子目录名称
 */
$lotus->option['app_name'] = 'app_name1';
/**
 * 是否使用MVC
 */
$lotus->mvcMode = true;
/**
 * 是否显示调试信息
 */
$lotus->debug = true;
/**
 * 使用cache可以提升性能
 */
//$lotus->option["cache_server"] = array("adapter" => "phps", "host" => "/tmp/LtCache/proj_dir/app_name1/");
$lotus->init();
/**
 * 显示调试信息
 */
if($lotus->debug)
{
	echo "<!--totalTime: {$lotus->debugInfo['totalTime']}s  memoryUsage: {$lotus->debugInfo['memoryUsage']} devMode: {$lotus->debugInfo['devMode']}-->";
}
// ------------- 
//echo "\r\n<!--\r\n";
//print_r($lotus);
//$conf = LtObjectUtil::singleton("LtConfig");
//print_r($conf->getAll());
//echo "-->";