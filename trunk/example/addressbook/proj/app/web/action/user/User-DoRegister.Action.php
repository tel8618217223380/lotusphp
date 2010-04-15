<?php
class UserDoRegisterAction extends LtAction
{
	public function __construct()
	{
		parent::__construct();

/**
@todo 如何验证表单内数组变量？
*/

		$this->dtds['password'] = new LtValidatorDtd("密码",
			array(
				"min_length" => 6,
				"max_length" => 16,
				"mask" => "/^[a-z0-9]+$/i",
				"ban" => "/fuck/",
				),
			array(
				"min_length" => "%s最少%s个字符",
				"max_length" => "%s最多%s个字符",
				"mask" => "%s只能由数字或字组成",
				"ban" => "%s不能包含脏话"
				)
			);
		$this->dtds['repassword'] = new LtValidatorDtd("再次输入密码",
			array(
				"min_length" => 6,
				"max_length" => 16,
				"mask" => "/^[a-z0-9]+$/i",
				"ban" => "/fuck/",
			"equal_to"=>$_POST['data']['password'],
				),
			array(
				"min_length" => "%s最少%s个字符",
				"max_length" => "%s最多%s个字符",
				"mask" => "%s只能由数字或字组成",
				"ban" => "%s不能包含脏话",
			"equal_to"=>"两次输入的密码不相等",
				)
			);

			$this->data['baseurl'] = C('LtConfig')->get('baseurl');
			$this->data['forward'] = 'goback';
			$this->data['title'] = 'addressbook';

			$this->responseType = 'tpl';
			$this->layout = 'result';
	}

	public function execute()
	{
		$data = $this->context->post('data');
		$user = new MyUser;
		if(!empty($data['password']) && $data['password'] == $data['repassword'])
		{
			$data['password'] = md5($data['password']);
			unset($data['repassword']);
			$user->add($data);
			$this->message = "注册成功";
		}

		$this->data['forward'] = C('LtUrl')->generate('Default', 'Index');

		$this->data['title'] = 'addressbook';
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl');
	}
}
