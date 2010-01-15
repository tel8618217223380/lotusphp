<?php
class LtSessionConfig
{
	/**
	 * 可选值: file, sqlite, mysql,mysqli,apc,eaccelerator,memcache,xcache
	 * 
	 * @var string 
	 */
	public $adapter = "mysql";
	/**
	 * 用到的配置, 如不需要可忽略.
	 * 
	 * @var array('option'=>'value', ...)
	 */
	 //file
	 //public $options = array("session_save_path" => "/tmp/LtSession");
	 
	 //mysql
	 public $options = array(
		 "host"=>"localhost",
		 "user"=>"root",
		 "password"=>"123456",
		 "table"=>"lotus_sessions",
		 "dbname"=>"test"
	 );
	//sqlite
	//public $options = array("host"=>"/tmp/Ltsession/","table"=>"lotus_sessions","dbname"=>"test.db");

}
