<?php
class Url
{
	private $defaultConf;

	public $conf;

	public function __construct()
	{
		$this->conf = new UrlConfig();
	}

	public function link($string)
	{
		echo isset($this->defaultConf->pattern) ? $this->defaultConf->pattern : $this->conf->pattern;
		echo "\tline " . $string . "\n";
	}

	static public function singleton($config = array())
	{
		static $instance = null;
		if (null === $instance)
		{
			$instance = new Url;
			$instance->defaultConf = new UrlConfig();
			foreach ($config as $key => $value)
			{
				$instance->defaultConf->$key = $value;
			}
		}
		return $instance;
	}
}