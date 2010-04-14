<?php
@set_magic_quotes_runtime(0);
date_default_timezone_set('Etc/GMT-8');
$lotusHome = substr(dirname(__FILE__), 0, strpos(__FILE__, "example"));

include $lotusHome . 'runtime/Lotus.php';
/**
 * 初始化Lotus类
 */
$lotus = new Lotus();
/**
 * 项目目录
 */
$lotus->option['proj_dir'] = dirname(dirname(__FILE__)) . '/proj/';
/**
 * 临时目录,默认是proj_dir/tmp/
 * 开发模式下的Autoloader 和 MVC的模板引擎 及 文件类型Cache
 */
//$lotus->option['app_tmp'] = $_SERVER['DOCUMENT_ROOT'].'/tmp/addressbook/';
$lotus->option['app_tmp'] = '/tmp/addressbook/';

/**
 * 是否自动加载函数文件, 默认为AutoloaderConfig.php的设置
 */
$lotus->option['load_function'] = true;
/**
 * 应用名称对项目目录下的子目录名称
 */
$lotus->option['app_name'] = 'app_web';
$lotus->option['runtime_filemap'] = true;

/**
 * 默认 true
 */
//$lotus->mvcMode = true;

/**
 * 默认 false;
 */
//$lotus->devMode = true;


$lotus->init();
/**
 * 使用xdebug测试性能
 */
if (function_exists('xdebug_time_index') && function_exists('xdebug_peak_memory_usage'))
{
	echo "\n<script type=\"text/javascript\">\n";
	echo 'document.getElementById("debug_info").innerHTML = "';
	echo xdebug_time_index();
	echo ' - ';
	echo format_size(xdebug_peak_memory_usage());
	echo "\";\n</script>\n";
}
