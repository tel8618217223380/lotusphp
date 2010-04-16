<?php
class UserCheckAction extends LtAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'json';
	}

	public function execute()
	{
		$mobile = $this->context->get('mobile');

		$user = new UserDao;
		if($user->exists($mobile, 'mobile'))
		{
			$this->message = "手机号已经注册";
		}
		else
		{
			$this->message = "可以使用";
		}
	}
}
