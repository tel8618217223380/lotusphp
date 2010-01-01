<?php
class LtDbSqlMapClient
{
	public $dbh;

	/**
	 * @todo 实现sql map的存储
	 */
	public function execute($mapId, $bind = null)
	{
		$testMap = array(
			"getAgeTotal" => array(
				"sql" => "SELECT COUNT(DISTINCT age) as age_total FROM test_user",
				"force_use_master" => true, 
			),
			"sys.getSysCateTotal" => array(
				"sql" => "SELECT COUNT(DISTINCT name) as category_total FROM sys_category",
				"force_use_master" => true, 
			)
		);
		$forceUseMaster = isset($testMap[$mapId]["force_use_master"]) ? $testMap[$mapId]["force_use_master"] : false;
		return $this->dbh->query($testMap[$mapId]["sql"], $bind, $forceUseMaster);
	}
}