<?php
class LtSession
{
	public static $saveHandle;
	public static $configHandle;

	public function __construct()
	{
		if (! self::$configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil", false))
			{
				self::$configHandle = LtObjectUtil::singleton("LtConfig");
			}
			else
			{
				self::$configHandle = new LtConfig;
			}
		}
	}

	public function init()
	{
		if(!$sessionSavePath = self::$configHandle->get("session.save_path"))
		{
			$sessionSavePath = '/tmp/Lotus/session/';
		}
		if (!is_object(self::$saveHandle))
		{
			ini_set('session.save_handler', 'files');
			if (!is_dir($sessionSavePath))
			{
				if (!@mkdir($sessionSavePath, 0777, true))
				{
					trigger_error("Can not create $sessionSavePath");
				}
			}
			session_save_path($sessionSavePath);
		}
		else
		{
			self::$saveHandle->conf = self::$configHandle->get("session.conf");
			self::$saveHandle->init();
			session_set_save_handler(
				array(&self::$saveHandle, 'open'), 
				array(&self::$saveHandle, 'close'),
				array(&self::$saveHandle, 'read'), 
				array(&self::$saveHandle, 'write'), 
				array(&self::$saveHandle, 'destroy'), 
				array(&self::$saveHandle, 'gc')
				);
		}
		//session_start();
		//header("Cache-control: private"); // to overcome/fix a bug in IE 6.x
	}
}
