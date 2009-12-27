<?php
/**
 * Lotus class autoloading
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";
include $lotusHome . "/runtime/Autoloader/AutoloaderConfig.php";
$autoloader = new LtAutoloader();
$autoloader->autoloadPath = $lotusHome . "/runtime";
$autoloader->init();

/**
 * PHPUnit bootstrap
 */
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Extensions/PerformanceTestCase.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');
require 'PHPUnit/TextUI/Command.php';
define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');