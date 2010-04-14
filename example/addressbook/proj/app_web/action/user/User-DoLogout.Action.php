<?php
class UserDoLogoutAction extends MyAction
{
	public function execute()
	{
		C('LtCookie')->delCookie('auth');
		$this->code = 200;
		$this->data['forward'] = C('Lturl')->generate('User', 'Login');

		$this->data['baseurl'] = C('LtConfig')->get('baseurl'); 
		$this->data['title'] = 'ÍË³ö³É¹¦';

		$this->responseType = 'tpl';
		$this->layout = 'result';
	}
}
