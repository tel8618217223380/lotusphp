<?php
class LtUrl
{
	public $conf;

	public function __construct()
	{
		$this->conf = new LtUrlConfig();
	}

	public function link($string)
	{
		echo "\tline " . $string . "\n";
		print_r($this->conf);
	}
}