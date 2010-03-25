<?php
/**
 * 本测试文档演示了LtValidator的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseValidator extends PHPUnit_Framework_TestCase
{
	public function testMostUsedWay()
	{
		$username = '123fuck 4567890';
		$password = '123fuck 4567890';

		$dtds['username'] = new LtValidatorDtd("username",
			array("max_length" => 8,
				"mask" => "/^[a-z0-9]+$/i",
				"ban" => "/fuck/",
				),
			array(
				// "max_length" 使用默认的错误消息
				"mask" => "用户名只能由数字或字组成",
				"ban" => "用户名不能包含脏话"
				)
			);

		$dtds['password'] = new LtValidatorDtd("password",
			array("max_length" => 8,
				"mask" => "/^[a-z0-9]+$/i",
				"ban" => "/fuck/",
				),
			array("max_length" => "密码最长8位",
				"mask" => "密码只能由数字或字组成",
				//"ban" => "密码不能包含脏话"
				)
			);

		$validator = new LtValidator;

		foreach ($dtds as $variable => $dtd)
		{
			foreach ($dtd->rules as $ruleKey => $ruleValue)
			{
				if ($ruleValue instanceof ConfigExpression)
				{
					eval('$_ruleValue = ' . $ruleValue->__toString());
					$dtd->rules[$ruleKey] = $_ruleValue;
				}
			}
			$error_messages = $validator->validate($$variable, $dtd);
			if (!empty($error_messages))
			{
				print_r($error_messages);
			}
		}
	}

	protected function setUp()
	{

	}

	protected function tearDown()
	{

	}
}
