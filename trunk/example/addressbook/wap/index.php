<?php
// set_magic_quotes_runtime(0);
if (version_compare(PHP_VERSION, '6.0.0-dev', '<') && get_magic_quotes_gpc())
{
	$in = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list ($k, $v) = each($in))
	{
		foreach ($v as $key => $val)
		{
			if (! is_array($val))
			{
				$in[$k][$key] = stripslashes($val);
				continue;
			}
			$in[] = &$in[$k][$key];
		}
	}
	unset($in);
}
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
 * 应用目录
 */
$lotus->option['app_dir'] = dirname(dirname(__FILE__)) . '/proj/app/';
/**
 * 应用名称对项目目录下的子目录名称
 * 如果只有一个应该可以不用设置
 */
$lotus->option['app_name'] = 'wap';
/**
 * 临时目录,默认是proj_dir/tmp/
 * 开发模式下的Autoloader 和 MVC的模板引擎 及 文件类型Cache
 */
// $lotus->option['app_tmp'] = $_SERVER['DOCUMENT_ROOT'].'/tmp/addressbook/';
$lotus->option['app_tmp'] = '/tmp/addressbook/';

/**
 * 是否自动加载函数文件, 默认为AutoloaderConfig.php的设置
 */
$lotus->option['load_function'] = true;
$lotus->option['runtime_filemap'] = true;

/**
 * 是否使用MVC
 */
// $lotus->mvcMode = true;
// $lotus->devMode = true;
$lotus->init();

/**
 * 使用xdebug测试性能
 */
$info = '';
if (function_exists('xdebug_time_index') && function_exists('xdebug_peak_memory_usage'))
{
	$info = xdebug_time_index();
	$info .= ' - ';
	$info .= format_size(xdebug_peak_memory_usage());
}

echo <<<END
<script type="text/javascript">
//<![CDATA[
$("#debuginfo").html("$info");
parent.$("#debuginfo").html("$info");
//]]>
</script>
END;

