<?php
class LtDbSqlMapClient
{
	public $dbh;

	public function execute($mapId, $bind = null)
	{
		$testMap = array(
			"getAgeTotal" => array(
				"sql" => "SELECT COUNT(DISTINCT age) as age_total FROM test_user",
				"force_use_master" => true, 
			)
		);
		$forceUseMaster = isset($testMap[$mapId]["force_use_master"]) ? $testMap[$mapId]["force_use_master"] : false;
		return $this->dbh->query($testMap[$mapId]["sql"], $bind, $forceUseMaster);
	}

	public function executeGroup($mapId, $bind = null)
	{
		$testmap = array(
			"dumpTestUser" => array(
				"sql" => "",
			)
		);
	}
}