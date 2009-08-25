<?php
class Url
{
	public $conf;

	public function __construct()
	{
		$this->conf = new UrlConfig();
	}

	public function link($string)
	{
		echo "\tline " . $string . "\n";
		print_r($this->conf);
	}
}