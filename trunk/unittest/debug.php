<?php
/**
 * 利用测试用例单步调试的模板
 */
$lotusHome = dirname(dirname(__FILE__));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";
include $lotusHome . "/runtime/Autoloader/AutoloaderConfig.php";
$autoloader = new LtAutoloader(array($lotusHome . "/runtime"));

/**
 * PHPUnit bootstrap
 */
set_include_path(".;D:\kiss\PHP\PEAR");//PHPUnit所在的位置，改成你自己的
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');
require 'PHPUnit/TextUI/Command.php';
define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

/**
 * 调试代码
 */
include './Autoloader/WrongWayToUse.php';
$i = new WrongWayToUseAutoloader();
$i->testDumplicateNameOfFunctions();