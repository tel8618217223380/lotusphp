<?php
class UserDoLoginAction extends LtAction
{
	public function execute()
	{
		$password = md5($this->context->post('password'));
		$username = trim($this->context->post('username'));
		if ($username && $password)
		{
			$user = new MyUser;
			$id = $user->getid($username);

			if ($id)
			{
				$data = $user->get($id);
			}
			else
			{
				$data = array();
			}

			if ($data && $data['password'] == $password)
			{
				$authCode = $data['uid'] . "\t" . $data['password'];
				$this->message = "µÇÂ½³É¹¦";
			}
			else
			{
				$this->message = "ÕÊºÅÃÜÂë´íÎó";
				$authCode = '';
			}
			/**
			 * ±£´æÊ±¼ä86400Ãë(Ò»Ìì)
			 */
			C("LtCookie")->setCookie('auth', $authCode, time() + 86400);
			$this->data['forward'] = C('LtUrl')->generate('default', 'index');
		}
		else
		{
			$this->message = "ÇëÊäÈëÕÊºÅºÍÃÜÂëµÇÂ½";
			$this->data['forward'] = C('LtUrl')->generate('User', 'Login');
		}

		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl'); 
		$this->data['title'] = 'ÓÃ»§µÇÂ½';

		$this->responseType = 'tpl';
		$this->layout = 'result';
	}
}
