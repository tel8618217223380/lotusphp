<?php
//先测试各组件
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Autoloader/AllTest.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Cache/AllTest.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Config/AllTest.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "DB/AllTest.php";

//最后测试Lotus框架
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Lotus/AllTest.php";

class AllTest
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit_Framework');

        $suite->addTestSuite('AutoloaderAllTest');
        $suite->addTestSuite('CacheAllTest');
        $suite->addTestSuite('ConfigAllTest');
        $suite->addTestSuite('DbAllTest');
        $suite->addTestSuite('LotusAllTest');

        return $suite;
    }
}