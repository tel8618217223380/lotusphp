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

	public function getSqlMap($group = null)
	{
		
	}

	public function getDbHandle($group = null)
	{
		$dbh = new LtDbHandle();
		$dbh->group = $group;
		$dbh->init();
		return $dbh;
	}
}