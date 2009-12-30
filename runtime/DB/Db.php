<?php
/**
 * @todo mysql/firebird/mssql等初始化配置的时候，schema = dbname, dbname = ""
 */
class LtDb
{
	protected $dbh;
	public $group;
	public $node;

	public function init()
	{
		$this->dbh = new LtDbHandle;
		$this->dbh->group = $this->getGroup();
		$this->dbh->node = $this->getNode();
	}

	public function getDbHandle()
	{
		return $this->dbh;
	}

	public function getTableGateway($table)
	{
		
	}

	protected function getGroup()
	{
		if (1 == count(LtDbStaticData::$servers))
		{
			return key(LtDbStaticData::$servers);
		}
	}

	protected function getNode()
	{
		if (1 == count(LtDbStaticData::$servers[$this->getGroup()]))
		{
			return key(LtDbStaticData::$servers[$this->getGroup()]);
		}
	}
}