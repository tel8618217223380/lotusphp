<?php
class LtDb
{
	public $configHandle;

	public function init()
	{
		
	}

	/**
	 * TG = Table Gateway
	 */
	public function getTG($table, $group = null)
	{
		
	}

	public function getSqlMap($group)
	{
		
	}

	public function getDbHandle($conf)
	{
		$dbh = new LtDbHandle();
		$dbh->conf = $conf;
		$dbh->init();
		return $dbh;
	}
}