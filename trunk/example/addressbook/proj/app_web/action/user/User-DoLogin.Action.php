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
				$this->message = "��½�ɹ�";
			}
			else
			{
				$this->message = "�ʺ��������";
				$authCode = '';
			}
			/**
			 * ����ʱ��86400��(һ��)
			 */
			C("LtCookie")->setCookie('auth', $authCode, time() + 86400);
			$this->data['forward'] = C('LtUrl')->generate('default', 'index');
		}
		else
		{
			$this->message = "�������ʺź������½";
			$this->data['forward'] = C('LtUrl')->generate('User', 'Login');
		}

		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl'); 
		$this->data['title'] = '�û���½';

		$this->responseType = 'tpl';
		$this->layout = 'result';
	}
}
