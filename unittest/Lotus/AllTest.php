<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "RightWayToUse.php";

class LotusAllTest
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit_Framework');

        $suite->addTestSuite('RightWayToUseLotus');

        return $suite;
    }
}