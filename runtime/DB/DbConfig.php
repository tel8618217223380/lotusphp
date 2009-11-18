<?php
class DbConfig
{
	protected $servers = array();

	protected $tables = array();

	public function addGroup($groupId)
	{
		$this->servers[$groupId] = array();
	}

	public function addNode($nodeId, $groupId)
	{
		$this->servers[$groupId][$nodeId] = array();
	}

	public function addHost($hostConfig, $role = "master", $nodeId, $groupId)
	{
		$this->servers[$groupId][$nodeId][$role][] = $hostConfig;
	}

	public function addSingleHost($hostConfig)
	{
		$this->addGroup("group_0");
		$this->addNode("node_0", "group_0");
		$this->addHost($hostConfig, "master", "node_0", "group_0");
	}

	public function addTable($tableId, $tableName = null, $schemaName = null, $groupId = null)
	{
		if (null === $tableName)
		{
			$tableName = $tableId;
		}
		if (1 == count($this->servers))
		{
			reset($this->servers);
			$groupId = key($this->servers);
		}
		$this->tables[$tableId] = array(
			"table_name" => $tableName,
			"schema" => $schemaName,
			"group" => $groupId
		);
	}

	public function getServers()
	{
		return $this->servers;
	}

	public function getTables()
	{
		return $this->tables;
	}	
}