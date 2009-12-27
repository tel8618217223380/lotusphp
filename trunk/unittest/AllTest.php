<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Autoloader/AllTest.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Cache/AllTest.php";

class AllTest
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit_Framework');

        $suite->addTestSuite('AutoloaderAllTest');
        $suite->addTestSuite('CacheAllTest');

        return $suite;
    }
}