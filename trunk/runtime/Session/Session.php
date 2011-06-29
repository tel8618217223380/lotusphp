<?php
class LtSession
{
	public $storeHandle;
	public $configHandle;
	protected static $started = false;

	public function __construct()
	{
		if (! $this->configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil", false))
			{
				$this->configHandle = LtObjectUtil::singleton("LtConfig");
			}
			else
			{
				$this->configHandle = new LtConfig;
			}
		}
	}

	public function init()
	{
		$sessionSaveHandle = $this->configHandle->get('session.save_handler');
		if(empty($sessionSaveHandle))
		{
			$sessionSaveHandle = 'files';
		}
		if(!self::$started)
		{
			$sessionClass = 'LtSession'.ucfirst($sessionSaveHandle);
			if(!class_exists($sessionClass))
			{
				trigger_error("$sessionClass Not Found!");
			}
			else
			{
				$session = new $sessionClass();
				$session->sessionSavePath = $this->configHandle->get('session.save_path');
				//session.name
				//session.gc_maxlifetime
				$session->init();
				self::$started = true;
			}
		}
	}
}
