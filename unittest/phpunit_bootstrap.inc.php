<?php
/**
 * 修改以下配置为你实际环境的值
 * 如果你修改了这两个常量的值，请不要svn commit
 */
define("PEAR_PATH","D:/kiss/PHP/PEAR");
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
function callWeb($url, $post, $withHeader = FALSE)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, LOTUS_UNITTEST_WEB_ROOT . $url);
	curl_setopt($ch, CURLOPT_HEADER, $withHeader);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	
	$reponse = curl_exec($ch);
	curl_close($ch);
	return $reponse;
}
/**
 * Lotus Error Handle
 */
//$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
//include $lotusHome . "error_handler/ErrorHandler.php";