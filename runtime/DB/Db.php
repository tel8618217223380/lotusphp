<?php
class LtDb
{
	static public $storeHandle;

	public $group;
	public $node;
	protected $dbh;

	public function init()
	{
		if (!is_object(self::$storeHandle))
		{
			self::$storeHandle = new LtDbStore;
		}
		$this->dbh = new LtDbHandle;
		$this->dbh->group = $this->getGroup();
		$this->dbh->node = $this->getNode();
		$this->dbh->init();
	}

	public function getDbHandle()
	{
		return $this->dbh;
	}

	public function getTDG($tableName)
	{
		$tg = new LtDbTable;
		$tg->tableName = $tableName;
		$tg->dbh = $this->dbh;
		return $tg;
	}

	public function getSqlMapClient()
	{
		$smc = new LtDbSqlMapClient;
		$smc->dbh = $this->dbh;
		return $smc;
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
		}elseif (1 == count(self::$storeHandle->get("servers")))
		{
			return key(self::$storeHandle->get("servers"));
		}
	}

	protected function getNode()
	{
		if ($this->node)
		{
			return $this->node;
		}
		$servers = self::$storeHandle->get("servers");
		if (1 == count($servers[$this->getGroup()]))
		{
			return key($servers[$this->getGroup()]);
		}
	}
}

class LtDbStore
{
	protected $stack;

	public function add($key, $value, $ttl = 0)
	{
		$this->stack[$key] = $value;
		return true;
	}

	public function del($key)
	{
		if (isset($this->stack[$key]))
		{
			unset($this->stack[$key]);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get($key)
	{
		return isset($this->stack[$key]) ? $this->stack[$key] : false;
	}

	public function update($key, $value, $ttl)
	{
		$this->stack[$key] = $value;
		return true;
	}
}
