<?php
/**
 * Database class
 */
class LtDb
{
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
		if (isset(LtDbStaticData::$tables[$table]) || 1 == count(LtDbStaticData::$servers))
		{
			$newDbTable = new LtDbTable();
			if (1 == count(LtDbStaticData::$servers))
			{
				$groupId = key(LtDbStaticData::$servers);
				$newDbTable->group = $groupId;
				$newDbTable->schema = $groupId;
				$newDbTable->tableName = $table;
			}
			else
			{
				$newDbTable->group = LtDbStaticData::$tables[$table]['group'];
				$newDbTable->schema = LtDbStaticData::$tables[$table]['schema'];
				if (isset(LtDbStaticData::$tables[$table]['created_column']))
				{
					$newDbTable->createdColumn = LtDbStaticData::$tables[$table]['created_column'];
				}
				if (isset(LtDbStaticData::$tables[$table]['modified_column']))
				{
					$newDbTable->modifiedColumn = LtDbStaticData::$tables[$table]['modified_column'];
				}
				if (isset(LtDbStaticData::$tables[$table]['table_name']))
				{
					$newDbTable->tableName = LtDbStaticData::$tables[$table]['table_name'];
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

