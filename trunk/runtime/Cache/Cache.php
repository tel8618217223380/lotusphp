<?php
class LtCache
{
	static public $servers;
	public $group;
	public $node;

	protected $ch;

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
		elseif (1 == count(self::$servers))
		{
			return key(self::$servers);
		}
	}

	protected function getNode()
	{
		if ($this->node)
		{
			return $this->node;
		}
		if (1 == count(self::$servers[$this->getGroup()]))
		{
			return key(self::$servers[$this->getGroup()]);
		}
	}
}