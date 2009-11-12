<?php
/* 
 * 加载Validator类文件
 */
$lotusHome = dirname(dirname(dirname(dirname(__FILE__))));
include $lotusHome . "/runtime/Validator/Validator.php";
include $lotusHome . "/runtime/Validator/ValidatorConfig.php";
/* 
 * 加载Validator类文件
 */

/*
 * 构造验证规则
 */

$dtd = array(
	"label" => "Username",
	"rules" => array(
		"required" => true,
		"max_length" => 4
	)
);
/*
 * 构造验证规则
 */

/*
 * 初始化Validator，执行验证
 */
$validator = new LtValidator;
$result = $validator->validate("",$dtd);
print_r($result);