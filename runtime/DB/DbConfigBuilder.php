<?php
class LtDbConfigBuilder
{
	protected $servers = array();

	protected $tables = array();

	protected $defaultConfig = array(
		"host"           => "localhost",          //some ip, hostname
		"port"           => 3306,
		"username"       => "root",
		"password"       => null,
		"adapter"        => "mysql",              //mysql,mysqli,pdo_mysql,sqlite,pdo_sqlite
		"charset"        => "UTF-8",
		"pconnect"       => false,                //true,false
		"connection_ttl" => 30,                   //any seconds
		"dbname"         => null,                 //default dbname
		"schema"         => null,                 //default schema
	);

	public function addSingleHost($hostConfig)
	{
		$this->addHost($hostConfig, "master", "node_0", "group_0");
	}

	public function addHost($hostConfig, $role = "master", $nodeId = "node_0", $groupId)
	{
		if (isset($this->servers[$groupId][$nodeId][$role]))
		{//以相同role的第一个host为默认配置
			$ref = $this->servers[$groupId][$nodeId][$role][0];
		}
		else if ("slave" == $role && isset($this->servers[$groupId][$nodeId]["master"]))
		{//slave host以master的第一个host为默认配置
			$ref = $this->servers[$groupId][$nodeId]["master"][0];
		}
		else if (isset($this->servers[$groupId]) && count($this->servers[$groupId]))
		{//以本group第一个node的master第一个host为默认配置
			$refNode = key($this->servers[$groupId]);
			$ref = $this->servers[$groupId][$refNode]["master"][0];
		}
		else if (count($this->servers))
		{//以第一个group第一个node的master第一个host为默认配置
			$refGroup = key($this->servers);
			$refNode = key($this->servers[$refGroup]);
			$ref = $this->servers[$refGroup][$refNode]["master"][0];
		}
		else
		{
			$ref = $this->defaultConfig;
		}//var_dump($ref);
		$conf = array_merge($ref, $hostConfig);
		$conf = $this->convertDbnameToSchema($conf);
		$this->servers[$groupId][$nodeId][$role][] = $conf;
	}

	public function getServers()
	{
		return $this->servers;
	}

	public function getTables()
	{
		return $this->tables;
	}

	public function buildTablesConfig()
	{

	}

	/**
	 * Convert dbname to schema for: FrontBase, MySQL, mSQL, MS SQL Server, MaxDB, Sybase
	 * See: http://www.php.net/manual-lookup.php?pattern=_select_db
	 */
	protected function convertDbnameToSchema($conf)
	{
		if (preg_match("/fbsql|mysql|msql|mssql|maxdb|sybase/i", $conf["adapter"]) && isset($conf["dbname"]))
		{
			$conf["schema"] = $conf["dbname"];
			$conf["dbname"] = null;
		}
		return $conf;
	}
}