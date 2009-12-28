<?php
/**
 * @todo mysql/firebird/mssql等初始化配置的时候，schema = dbname, dbname = ""
 */
class LtDb
{
	public $configHandle;
	public $group;
	public $node;
	public $table;
	public $tableGateway;//Table Gateway
	public $sqlMap;
	protected $connectionManager;
	protected $connectionResource;

	public function init()
	{
		$this->connectionManager = new LtDbConnectionManager;
	}

	/**
	 * raw query
	 */
	public function query($group = null)
	{
	}

	protected function prepareConnection()
	{
	}
}