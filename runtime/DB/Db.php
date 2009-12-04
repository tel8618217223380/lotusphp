<?php
/**
 * Database class
 */
class LtDb
{
	/**
	 * The connection pool
	 *
	 * @var array
	 */
	static public $connections = array();

	static public $servers;

	static public $tables;

	/**
	 * Factory for Adapter
	 * 
	 * @assert ('pdoMssql') throws Exception
	 * @assert ('') throws Exception
	 * @param string $driver
	 * @return object
	 */
	static public function factory($driver)
	{
		$adapterClassName = 'LtDbAdapter' . ucfirst($driver);
		if (!empty($driver))
		{
			if (class_exists($adapterClassName))
			{
				return new $adapterClassName();
			}
			else
			{
				DebugHelper::debug('DB_DRIVER_NOT_SUPPORTED', array('driver' => $driver));
			}
		}
		else
		{
			DebugHelper::debug('NO_DB_DRIVER_PASSED', array('driver' => $driver));
		}
	}

	/**
	 * Get an instance of a db table
	 *
	 * @assert ('some_does_not_exist_table') throws Exception
	 * @param string $table
	 * @param mixed $tableName
	 * @return object DbTable
	 */
	static public function newDbTable($table)
	{
		if (isset(LtDb::$tables[$table]) || 1 == count(LtDb::$servers))
		{
			$newDbTable = new LtDbTable();
			if (1 == count(LtDb::$servers))
			{
				$groupId = key(LtDb::$servers);
				$newDbTable->group = $groupId;
				$newDbTable->schema = $groupId;
				$newDbTable->tableName = $table;
			}
			else
			{
				$newDbTable->group = LtDb::$tables[$table]['group'];
				$newDbTable->schema = LtDb::$tables[$table]['schema'];
				if (isset(LtDb::$tables[$table]['created_column']))
				{
					$newDbTable->createdColumn = LtDb::$tables[$table]['created_column'];
				}
				if (isset(LtDb::$tables[$table]['modified_column']))
				{
					$newDbTable->modifiedColumn = LtDb::$tables[$table]['modified_column'];
				}
				if (isset(LtDb::$tables[$table]['table_name']))
				{
					$newDbTable->tableName = LtDb::$tables[$table]['table_name'];
				}
			}
			$newDbTable->db = $newDbTable->getAdapterInstance();
			$newDbTable->db->setGroup($newDbTable->group);
			$newDbTable->db->setSchema($newDbTable->schema);
			return $newDbTable;
		}
		else
		{
			DebugHelper::debug('DB_TABLE_CONFIG_IS_MISSING', array('table' => $table));
		}
	}
}

