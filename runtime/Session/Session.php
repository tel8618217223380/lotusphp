<?php
class LtSession
{
	static public $saveHandle;
	public $conf;

	public function __construct()
	{
		$this->conf = new LtSessionConfig;
	}

	public function init()
	{
		if (!is_object(self::$saveHandle))
		{
			ini_set('session.save_handler', 'files');
			if (!is_dir($this->conf['session_save_path']))
			{
				if (!@mkdir($this->conf['session_save_path'], 0777, true))
				{
					trigger_error("Can not create $cachePath");
				}
			}
			session_save_path($this->conf['session_save_path']);
		}
		else
		{
			session_set_save_handler(
				array(&self::$saveHandle, 'open'), 
				array(&self::$saveHandle, 'close'),
				array(&self::$saveHandle, 'read'), 
				array(&self::$saveHandle, 'write'), 
				array(&self::$saveHandle, 'destroy'), 
				array(&self::$saveHandle, 'gc')
				);
		}
		session_start();
		header("Cache-control: private"); // to overcome/fix a bug in IE 6.x
	}
}
