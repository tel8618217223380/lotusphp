<?php
class LtSessionConfig
{
	/**
	 * 可选值: file, sqlite, mysql,mysqli,apc,eaccelerator,memcache,xcache
	 * 
	 * @var string 
	 */
	public $adapter = "file";
	/**
	 * 用到的配置, 如不需要可忽略.
	 * 
	 * @var array('option'=>'value', ...)
	 */
	public $options = array("session_save_path" => "/tmp/LtSession");
}
