<?php
class LtCache
{
	static public $configHandle;

	public $group;
	public $node;

	protected $ch;

	public function __construct()
	{
		self::$configHandle = new LtConfig;
	}

	public function init()
	{
		$this->ch = new LtCacheHandle;
		$this->ch->group = $this->getGroup();
		$this->ch->node = $this->getNode();
	}

	public function getTDG($tableName)
	{
		$tdg = new LtCacheTableDataGateway;
		$tdg->tableName = $tableName;
		$tdg->ch = $this->ch;
		return $tdg;
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
		$servers = self::$configHandle->get("cache.servers");
		if (1 == count($servers))
		{
			return key($servers);
		}
		return false;
	}

	protected function getNode()
	{
		if ($this->node)
		{
			return $this->node;
		}
		$servers = self::$configHandle->get("cache.servers");
		if (1 == count($servers[$this->getGroup()]))
		{
			return key($servers[$this->getGroup()]);
		}
		return false;
	}
}