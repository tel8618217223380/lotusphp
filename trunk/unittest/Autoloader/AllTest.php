<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "RightWayToUse.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "WrongWayToUse.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "PerformanceTest.php";

class AutoloaderAllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit_Framework');

        $suite->addTestSuite('RightWayToUseAutoloader');
        $suite->addTestSuite('WrongWayToUseAutoloader');
        $suite->addTestSuite('PerformanceTest4Autoloader');

        return $suite;
    }
}