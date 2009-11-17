<?php
class DbServer
{
	protected $servers = array();

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

	public function getServers()
	{
		return $this->servers;
	}
}