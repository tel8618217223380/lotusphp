<?php
/**
 * Database adapter PDO pgsql class
 */
class LtDbAdapterPdoPgsql extends LtDbAdapterPdo
{
	/**
	 * The default database configuration for pgsql
	 *
	 * @var array
	 */
	protected $_config =  array(
		'port' => '5432',
		'username' => 'postgres'
	);

	/**
	 * Create a PDO DSN for the adapter
	 * 
	 * @param array $config 
	 * @return string 
	 */
	protected function _dsn($config)
	{
		$config['port'] = isset($config['port']) ? $config['port'] : '5432';
		return "pgsql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['dbname'] . ";user=" . $config['username'] . ";password=" . $config['password'];
	}

	/**
	 * Get current db configuration
	 *
	 * @param string $group
	 * @param string $node
	 * @param string $role
	 * @param string $host
	 * @return array
	 */
	protected function _getConfig($group, $node, $role = 'master', $host = null)
	{
		return $this->_getBasicConfig($group, $node, $role, $host);
	}

	/**
	 * Change current schema
	 *
	 * @return void
	 */
	protected function _useSchema()
	{
		$sql = "SET SEARCH_PATH TO '$this->_schema';";
		$this->_connection->exec($sql);
	}

	/**
	 * Add an adapter-specific LIMIT clause to the SELECT statement.
	 * 
	 * @param string $sql 
	 * @param integer $limit 
	 * @param integer $offset 
	 * @return string 
	 */
	public function limit($sql, $limit, $offset)
	{
		if ($limit > 0)
		{
			$offset = 0 < $offset ? $offset : 0;
			$sql .= " LIMIT $limit OFFSET $offset";
		}
		return $sql;
	}

	/**
	 * Get the last inserted ID.
	 * 
	 * @param string $tableName 
	 * @param string $primaryKey 
	 * @return integer 
	 */
	public function lastInsertId($tableName = null, $primaryKey = null)
	{
		return $this->_connection->lastInsertId($tableName . '_' . $primaryKey . '_seq');
	}

	/**
	 * Set encoding for a database connection.
	 * 
	 * @param string $encoding 
	 * @param resource $connection 
	 * @return void 
	 */
	public function setCharset($charset, $connection)
	{
		$sql = "SET CLIENT_ENCODING TO '$charset'";
		$connection->exec($sql);
	}

	/**
	 * Return the column descriptions for a table.
	 * 
	 * @param string $table
	 * @return array 
	 */
	public function showFields($table)
	{
		$sql = "SELECT a.attnum, a.attname AS field, t.typname AS type, format_type(a.atttypid, a.atttypmod) AS complete_type, "
		 . "a.attnotnull AS isnotnull, "
		 . "( SELECT 't' "
		 . 'FROM pg_index '
		 . "WHERE c.oid = pg_index.indrelid "
		 . "AND pg_index.indkey[0] = a.attnum "
		 . "AND pg_index.indisprimary = 't') AS pri, "
		 . "(SELECT pg_attrdef.adsrc "
		 . 'FROM pg_attrdef '
		 . "WHERE c.oid = pg_attrdef.adrelid "
		 . "AND pg_attrdef.adnum=a.attnum) AS default "
		 . "FROM pg_attribute a, pg_class c, pg_type t "
		 . "WHERE c.relname = '{$table}' "
		 . 'AND a.attnum > 0 '
		 . "AND a.attrelid = c.oid "
		 . "AND a.atttypid = t.oid "
		 . 'ORDER BY a.attnum ';
		$queryResult = $this->query($sql);
		$result = $queryResult['rows'];
		$fields = array();
		foreach ($result as $key => $val)
		{
			if ($val['type'] === 'varchar')
			{
				$length = preg_replace('~.*\(([0-9]*)\).*~', '$1', $val['complete_type']);
				$val['type'] .= '(' . $length . ')';
			}
			$fields[$val['field']]['name'] = $val['field'];
			$fields[$val['field']]['type'] = $val['type'];
			$fields[$val['field']]['notnull'] = ($val['isnotnull'] == '');
			$fields[$val['field']]['default'] = $val['default'];
			$fields[$val['field']]['primary'] = ($val['pri'] == 't');
		}
		return $fields;
	}
}
