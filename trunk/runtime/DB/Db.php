<?php
/**
 * Database class
 */
class Db
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
		$adapterClassName = 'DbAdapter' . ucfirst($driver);
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
		if (isset(Db::$tables[$table]) || 1 == count(Db::$servers))
		{
			$newDbTable = new DbTable();
			if (1 == count(Db::$servers))
			{
				$groupId = key(Db::$servers);
				$newDbTable->group = $groupId;
				$newDbTable->schema = $groupId;
				$newDbTable->tableName = $table;
			}
			else
			{
				$newDbTable->group = Db::$tables[$table]['group'];
				$newDbTable->schema = Db::$tables[$table]['schema'];
				if (isset(Db::$tables[$table]['created_column']))
				{
					$newDbTable->createdColumn = Db::$tables[$table]['created_column'];
				}
				if (isset(Db::$tables[$table]['modified_column']))
				{
					$newDbTable->modifiedColumn = Db::$tables[$table]['modified_column'];
				}
				if (isset(Db::$tables[$table]['table_name']))
				{
					$newDbTable->tableName = Db::$tables[$table]['table_name'];
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

