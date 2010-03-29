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
$lotus->option['app_tmp'] = '/tmp/Lotus/';

/**
 * 应用名称对项目目录下的子目录名称
 */
$lotus->option['app_name'] = 'app_name1';
/**
 * 默认使用MVC
 * $lotus->mvcMode = true;
 */

/**
 * 使用cache可以提升性能
 */
//$lotus->option["app_cache"] = array("adapter" => "phps", "host" => "/tmp/Lotus/lotus/proj_dir/app_name1/");

$lotus->init();
/**
 * 使用xdebug测试性能
 */
if (function_exists('xdebug_time_index') && function_exists('xdebug_peak_memory_usage'))
{
	echo xdebug_time_index();
	echo ' - ';
	echo format_size(xdebug_peak_memory_usage());
}

function format_size($size)
{
	if ($size >= 1073741824)
	{
		$size = round($size / 1073741824, 2) . ' GB';
	}
	else if ($size >= 1048576)
	{
		$size = round($size / 1048576, 2) . ' MB';
	}
	else if ($size >= 1024)
	{
		$size = round($size / 1024, 2) . ' KB';
	}
	else
	{
		$size = round($size, 2) . ' Bytes';
	}
	return $size;
}
