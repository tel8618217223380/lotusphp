<?php
/**
 * 加载Validator类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Validator/Validator.php";
include $lotusHome . "/runtime/Validator/ValidatorConfig.php";
include $lotusHome . "/runtime/Validator/ValidatorDtd.php";

class Action {
	public $test;
} 

class MyAction extends Action {
	public function demo() {
		/**
		 * 构造验证规则
		 */
		$dtd = new LtValidatorDtd("用户名",
			array("max_length" => 4,
				"mask" => "/^[a-z0-9]+$/i",
				"ban" => "/fuck/",
				"callback_check_user" => 'MyAction',
				),
			array(
				// "max_length" 使用默认的错误消息，在$this->conf->errorMessage里
				"mask" => "用户名只能由数字或字组成",
				"ban" => "用户名不能包含脏话",
				"callback_check_user" => "方法 用户存在",
				)
			);

		/**
		 * 初始化Validator，执行验证
		 */
		$validator = new LtValidator;
		$username = "fuck my life";
		$result = $validator -> validate($username, $dtd);
		print_r($result);
	} 

	public function check_user($username) {
		// 可以从数据库查询
		if ('fuck my life' == $username) {
			return false;
		} 
		return true;
	} 
} 

$a = new MyAction();
$a -> demo();

