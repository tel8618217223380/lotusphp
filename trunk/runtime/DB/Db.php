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

	/**
	 * The default database configuration
	 *
	 * @var array
	 */
	static public $defaultConfig = array(
		'host' => 'localhost',
		'password' => '');

	/**
	 * Constructor placeholder
	 * Prevent the Db class from instantiating
	 * Singleton Pattern
	 */
	private function __construct()
	{
	}

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
	static public function newDbTable($table, $tableName = null)
	{
		if (class_exists($table))
		{
			return new $table;
		}
		if (isset(Config::$app["db_table"][$table]))
		{
			$newDbTable = new DbTable();
			$newDbTable->group = Config::$app["db_table"][$table]['group'];
			$newDbTable->schema = Config::$app["db_table"][$table]['schema'];
			if (isset(Config::$app["db_table"][$table]['created_column']))
			{
				$newDbTable->createdColumn = Config::$app["db_table"][$table]['created_column'];
			}
			if (isset(Config::$app["db_table"][$table]['modified_column']))
			{
				$newDbTable->modifiedColumn = Config::$app["db_table"][$table]['modified_column'];
			}
			if (isset(Config::$app["db_table"][$table]['table_name']))
			{
				$newDbTable->tableName = Config::$app["db_table"][$table]['table_name'];
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

	/**
	 * Set default config
	 *
	 * @param array $config
	 * @return void
	 */
	static public function setDefaultConfig($config)
	{
		self::$defaultConfig = array_merge(self::$defaultConfig, $config);
	}
}

