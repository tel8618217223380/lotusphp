<?php
/**
 * PHPUnit bootstrap
 */
//把自定义的搜索路径放默认的搜索路径后边,优先使用在php.ini中设置路径
set_include_path(get_include_path() . PATH_SEPARATOR . "." . PATH_SEPARATOR ."D:/kiss/PHP/PEAR");
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Extensions/PerformanceTestCase.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');
require_once 'PHPUnit/TextUI/Command.php';
define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');