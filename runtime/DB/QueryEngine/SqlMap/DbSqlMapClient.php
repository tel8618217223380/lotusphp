<?php
class LtDbSqlMapClient
{
	public $dbh;

	public function execute($mapId, $bind = null)
	{
		$sqlMap = LtDb::$configHandle->get($this->dbh->group . "." . $mapId);
		$forceUseMaster = isset($sqlMap["force_use_master"]) ? $sqlMap["force_use_master"] : false;
		return $this->dbh->query($sqlMap["sql"], $bind, $forceUseMaster);
	}
}