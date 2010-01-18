<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
require_once $lotusHome . "/runtime/Validator/Validator.php";
require_once $lotusHome . "/runtime/Validator/ValidatorConfig.php";
require_once $lotusHome . "/runtime/Validator/ValidatorDtd.php";

/**
 * 用这个类把LtValidator的protected属性和方法暴露出来测试
 */
class LtValidatorProxy extends LtValidator
{

}