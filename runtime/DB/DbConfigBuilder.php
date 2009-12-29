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
		"dbname"         => null,                   //default dbname
		"schema"         => null,                 //default schema
	);

	public function addGroup($groupId)
	{
		$this->servers[$groupId] = array();
	}

	public function addNode($nodeId, $groupId)
	{
		$this->servers[$groupId][$nodeId] = array();
	}

	public function addHost($hostConfig, $role = "master", $nodeId = "node_0", $groupId)
	{
		if (null === $role)
		{
			$role = "master";
		}
		if (null === $nodeId)
		{
			$nodeId = "node_0";
		}
		$this->servers[$groupId][$nodeId][$role][] = $hostConfig;
	}

	public function addSingleHost($hostConfig)
	{
		$this->addGroup("group_0");
		$this->addNode("node_0", "group_0");
		$this->addHost($this->buildHostConfig("group_0", "node_0", "master", $hostConfig), "master", "node_0", "group_0");
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

	protected function buildHostConfig($groupId, $nodeId, $role, $hostConfig)
	{
		$conf = array_merge($this->defaultConfig, $hostConfig);
		if (preg_match("~mysql~", $hostConfig["adapter"]))
		{
			$conf["schema"] = $conf["dbname"];
			$conf["dbname"] = null;
		}
		return $conf;
	}
}