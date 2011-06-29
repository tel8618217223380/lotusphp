<?php
class LtSessionFiles
{
	public $sessionSavePath;

	public function init()
	{
		ini_set('session.save_handler', 'files');
		if (empty($this->sessionSavePath))
		{
			$this->sessionSavePath = '/tmp/Lotusphp.session';			
		}
		if (!is_dir($this->sessionSavePath))
		{
			if (!@mkdir($this->sessionSavePath, 0777, true))
			{
				trigger_error("Can not create $this->sessionSavePath");
			}
		}
		ini_set('session.save_path', $this->sessionSavePath);
		session_start();
	}
}