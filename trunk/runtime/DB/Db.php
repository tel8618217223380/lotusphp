<?php
/**
 * @todo mysql/firebird/mssql等初始化配置的时候，schema = dbname, dbname = ""
 */
class LtDb
{
	static public $storeHandle;
	static public $namespace = "";
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
	}

	public function getDbHandle()
	{
		return $this->dbh;
	}

	public function getTableGateway($tableName)
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
		}
		elseif (1 == count(self::$storeHandle->get("servers", self::$namespace)))
		{
			return key(self::$storeHandle->get("servers", self::$namespace));
		}
	}

	protected function getNode()
	{
		if ($this->node)
		{
			return $this->node;
		}
		$servers = self::$storeHandle->get("servers", self::$namespace);
		if (1 == count($servers[$this->getGroup()]))
		{
			return key($servers[$this->getGroup()]);
		}
	}
}

class LtDbStore
{
	protected $stack;

	public function add($key, $value, $ttl, $namespace)
	{
		$this->stack[$this->getRealKey($namespace, $key)] = $value;
		return true;
	}

	public function del($key, $namespace)
	{
		$key = $this->getRealKey($namespace, $key);
		if(isset($this->stack[$key]))
		{
			unset($this->stack[$key]);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get($key, $namespace)
	{
		$key = $this->getRealKey($namespace, $key);
		return isset($this->stack[$key]) ? $this->stack[$key] : false;
	}

	public function update($key, $value, $ttl, $namespace)
	{
		$this->stack[$this->getRealKey($namespace, $key)] = $value;
		return true;
	}

	protected function getRealKey($namespace, $key)
	{
		return sprintf("%u", crc32($namespace)) . $key;
	}
}