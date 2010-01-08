<?php
/**
 * PHPUnit bootstrap
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "." . PATH_SEPARATOR ."D:/kiss/PHP/PEAR");
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Extensions/PerformanceTestCase.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');
require_once 'PHPUnit/TextUI/Command.php';
define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

/**
 * Lotus Error Handle
 */
//$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
//include $lotusHome . "/error_handler/ErrorHandler.php";