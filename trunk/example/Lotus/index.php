<?php
$lotusHome = substr(dirname(__FILE__), 0, strpos(__FILE__, "example"));
include $lotusHome . 'runtime/Lotus.php';

$lotus = new Lotus();

$lotus->option['proj_dir'] = dirname(__FILE__) . '/proj_dir/';
$lotus->option['app_name'] = 'app_name1';
/**
 * 注意：默认是使用mvc 开发模式关闭 
 * 因此，修改配置需要手工删除缓存目录内生成的文件
 */
$lotus->option['app_tmp'] = '/tmp/Lotus/';
/**
 * 配置LtAutoloader组件是否将runtime目录的类文件映射保存到局部变量内,
 * 启用后Autoloader的自动加载方法先查找局部变量,然后再到storeHandle查找.
 * 
 * 这是可选的, 如不需要, 删除下面一行代码

 * 使用后初始化时间会稍大,内存占用会稍高,
 * 减少的是创建类实例时每个文件读一次取文件路径的时间.
 */
$lotus->option['runtime_filemap'] = true; //删除此行 同时请删除缓存
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
