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
		"max_length" => 4,
		"mask" => "/^[a-z0-9]+$/i",
		"ban" => "/fuck/",
	),
	"message" => array(
		//"max_length" 使用默认的错误消息，在$this->conf->errorMessage里
		"mask" => "户名只能由数字或字组成",
		"ban" => "用户名不能包含脏话"
	)
);
/*
 * 构造验证规则
 */

/*
 * 初始化Validator，执行验证
 */
$validator = new LtValidator;
$result = $validator->validate("fuck my life",$dtd);
print_r($result);