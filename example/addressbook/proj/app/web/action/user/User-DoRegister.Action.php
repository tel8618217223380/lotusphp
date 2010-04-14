<?php
class UserDoRegisterAction extends LtAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl';
		$this->layout = 'result';
	}

	public function execute()
	{
		$data = $this->context->post('data');
		$user = new MyUser;
		if($data['password'] == $data['repassword'])
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
