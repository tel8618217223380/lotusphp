<?php
class LtDb
{
	static public $configHandle;

	public $group;
	public $node;
	protected $dbh;

	public function __construct()
	{
		if (class_exists("LtObjectUtil"))
		{
			self::$configHandle = LtObjectUtil::singleton("LtConfig");
		}
		self::$configHandle = new LtConfig;		
	}

	public function init()
	{
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
		$tg = new LtDbTableDataGateway;
		$tg->tableName = $tableName;
		$tg->createdColumn = 'created';
		$tg->modifiedColumn = 'modified';
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
		$servers = self::$configHandle->get("db.servers");
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
		$servers = self::$configHandle->get("db.servers");
		if (1 == count($servers[$this->getGroup()]))
		{
			return key($servers[$this->getGroup()]);
		}
		return false;
	}
}