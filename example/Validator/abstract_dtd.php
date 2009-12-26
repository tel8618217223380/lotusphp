<?php
/* 
 * 加载Validator类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Validator/Validator.php";
include $lotusHome . "/runtime/Validator/ValidatorConfig.php";
include $lotusHome . "/runtime/Validator/ValidatorDtd.php";
/* 
 * 加载Validator类文件
 */

/*
 * 定义Dtd基类
 */
class MyValidatorDtdId extends LtValidatorDtd
{
	public function __construct($label, $rules, $messages = null)
	{
		$this->rules = array(
			"mask" => "/^[0-9]+$/",
		);
		$this->messages = array(
			"mask" => "%s只能是数字"
		);
		parent::__construct($label, $rules, $messages);
	}
}
/*
 * 构造验证规则
 */
$dtd["board_id"] = new MyValidatorDtdId("版块ID",//版块ID一般比较小
	array(
		"max_value" => 100,
	)
);
$dtd["thread_id"] = new MyValidatorDtdId("主题ID",//主题ID通常较大，但也不会超过10位
	array(
		"required" => true,
		"max_length" => 10,
	)
);
/*
 * 构造验证规则
 */

/*
 * 初始化Validator，执行验证
 */
$validator = new LtValidator;
$result["board_id"] = $validator->validate(987, $dtd["board_id"]);
$result["thread_id"] = $validator->validate("ajax123", $dtd["thread_id"]);
print_r($result);