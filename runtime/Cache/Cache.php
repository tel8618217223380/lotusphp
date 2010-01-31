<?php
class LtCache
{
	public $servers;
	public $group;
	public $node;

	protected $ch;

	public function init()
	{
		$this->servers["default_group"]["default_node"]["master"][] = array(
			"adapter" => "phps",
			"host"    => "/tmp/LtCache/",
		);
		$this->ch = new LtCacheHandle;
		$this->ch->connectionManager->servers = $this->servers;
		$this->ch->group = $this->getGroup();
		$this->ch->node = $this->getNode();
	}

	public function getCacheHandle()
	{
		return $this->ch;
	}

	public function changeNode($node)
	{
		$this->node = $node;
		$this->dbh->node = $node;
	}

	protected function getGroup()
	{
		if ($this->group)
		{
			return $this->group;
		}
		elseif (1 == count($this->servers))
		{
			return key($this->servers);
		}
	}

	protected function getNode()
	{
		if ($this->node)
		{
			return $this->node;
		}
		$servers = $this->servers;
		if (1 == count($servers[$this->getGroup()]))
		{
			return key($servers[$this->getGroup()]);
		}
	}
}