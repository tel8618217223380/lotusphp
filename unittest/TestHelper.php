<?php
/**
 * Lotus class autoloading
 */
$lotusHome = dirname(dirname(__FILE__));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";
include $lotusHome . "/runtime/Autoloader/AutoloaderConfig.php";
$autoloader = new LtAutoloader();
$autoloader->setAutoloadPath($lotusHome . "/runtime");
$autoloader->init();

/**
 * PHPUnit bootstrap
 */
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');
require 'PHPUnit/TextUI/Command.php';
define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');
PHPUnit_TextUI_Command::main();