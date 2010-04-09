<?php
// 检测是否可以创建并且删除子目录
if (is_dir('/tmp/Lotus/tmpdir'))
{
	if (!rmdir('/tmp/Lotus/tmpdir'))
	{
		exit('can not rmdir');
	}
}
if (!mkdir('/tmp/Lotus/tmpdir', 0777, true))
{
	exit('can not mkdir');
}
if (!rmdir('/tmp/Lotus/tmpdir'))
{
	exit('can not rmdir');
}
// 检测是否可以创建并且删除文件
if(!file_put_contents('/tmp/Lotus/tmpfile', 'Lotusphp'))
{
	exit('can not file_put_contents');
}
// chmod('/tmp/Lotus/tmpfile', 0777);
if(is_file('/tmp/Lotus/tmpfile'))
{
	if(!unlink('/tmp/Lotus/tmpfile'))
	{
		exit('can not unlink');
	}
}

/**
 * 修改以下配置为你实际环境的值
如果你修改了这两个常量的值，请不要svn commit
 */
define("PEAR_PATH", "D:/kiss/PHP/PEAR");
define("LOTUS_UNITTEST_WEB_ROOT", "http://lotus/unittest/");

/**
 * PHPUnit bootstrap
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "." . PATH_SEPARATOR . PEAR_PATH);
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Extensions/PerformanceTestCase.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');
require_once 'PHPUnit/TextUI/Command.php';
define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

/**
 * Web server gateway
 */
function callWeb($url, $post = null, $header = null, $returnHeader = false)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, LOTUS_UNITTEST_WEB_ROOT . $url);
	curl_setopt($ch, CURLOPT_HEADER, $returnHeader);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$header[] = "Expect:";//阻止lighttpd返回417错误
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

	$reponse = curl_exec($ch);
	curl_close($ch);
	return $reponse;
}

function formatSize($size)
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
/**
 * Lotus Error Handle
 */
// $lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
// include $lotusHome . "error_handler/ErrorHandler.php";
