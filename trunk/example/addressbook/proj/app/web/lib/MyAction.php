<?php
class MyAction extends LtAction
{
	public function __construct()
	{
		parent::__construct();
	}

	public function beforeExecute()
	{
		$this->context->admin_id = 0;
		$authCode = C('LtCookie')->getCookie('auth');
		if ($authCode)
		{
			list($id, $password) = explode("\t", $authCode);
			$user = new MyUser;
			$data = $user->get($id);
			if ($data && $data['password'] == $password)
			{
				$this->data['username'] = $data['username'];
				$this->data['uid'] = $data['uid'];
			}
			else
			{
				$this->data['username'] = '';
				$this->data['uid'] = 0;
				C('LtCookie')->delCookie('auth');
			}
		}
		if (!$this->data['uid'])
		{
			header("Location: " . C('LtUrl')->generate('user', 'login'));
		} 
	}
}
