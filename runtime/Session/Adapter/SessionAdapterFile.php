<?php
class LtSessionAdapterFile implements LtSessionAdapter
{
	public $options;
	
	function __construct()
	{
	}

	public function init()
	{
		ini_set('session.save_handler', 'files');

		if(!is_dir($this->options['session_save_path']))
		{
			if(!@mkdir($this->options['session_save_path'], 0777, true))
			{
				trigger_error("Can not create $cachePath");
			}
		}
		session_save_path($this->options['session_save_path']);
	}
}
