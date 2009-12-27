<?php

class LtDbConfig
{
	/**
	 * Db connection config
	 * @var $conn array
	 */
	public $conn = array(
	//--key--             --default value--     --optional value--
		"host"           => "localhost",          //some ip, hostname
		"port"           => 3306,
		"username"       => "root",
		"password"       => "",
		"adapter"        => "mysql",              //mysql,mysqli,pdo_mysql,sqlite,pdo_sqlite
		"charset"        => "UTF-8",
		"pconnect"       => false,                //true,false
		"connection_ttl" => 30,                   //any seconds
		"dbname"         => "",
		"schema"         => "",
	);
}