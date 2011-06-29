<?php
class LtSessionMemcache
{
	public $sessionSavePath;

	public function init()
	{
		ini_set('session.save_handler', 'memcache');
		if(empty($this->sessionSavePath))
		{
			$this->sessionSavePath = 'tcp://127.0.0.1:11211';
		}
		ini_set('session.save_path', $this->sessionSavePath);
		session_start();
	}

}
