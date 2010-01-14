<?php
class LtSession
{
	public $conf;

	public function __construct()
	{
		$this->conf = new LtSessionConfig;
	}

	public function init()
	{
		$adapterClassName = "LtSessionAdapter" . ucfirst($this->conf->adapter);
		if(!class_exists($adapterClassName))
		{
			trigger_error('Invalid adapter');
		}
		$this->sessionHandle = new $adapterClassName;
		if (property_exists($this->sessionHandle, "options"))
		{
			$this->sessionHandle->options = $this->conf->options;
		}
		$this->sessionHandle->init();
		session_start();
		header("Cache-control: private"); // to overcome/fix a bug in IE 6.x
	}
}
