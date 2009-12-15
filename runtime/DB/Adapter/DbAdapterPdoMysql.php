<?php
/**
 * Database adapter PDO mysql class
 */
class LtDbAdapterPdoMysql extends LtDbAdapterPdo
{
	/**
	 * The default database configuration for mysql
	 *
	 * @var array
	 */
	protected $_config =  array('port' => '3306', 'username' => 'root');

	/**
	 * The PDO construct options
	 *
	 * @var array
	 */
	protected $_options = array(PDO::ATTR_PERSISTENT => false);

	/**
	 * Create a PDO DSN for the adapter
	 *
	 * @param array $config
	 * @return string
	 */
	protected function _dsn($config)
	{
		$config['port'] = isset($config['port']) ? $config['port'] : '3306';
		return "mysql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['dbname'];
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
		$config = $this->_getBasicConfig($group, $node, $role, $host);
		$config['schema'] = $config['dbname'];
		$config['dbname'] = '';
		return $config;
	}

	/**
	 * Change current schema
	 *
	 * @return void
	 */
	protected function _useSchema()
	{
		$config = $this->_getConfig($this->getGroup(), $this->getNode());
		$this->_schema = $config['schema'];
		$sql = "USE $this->_schema";
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
	 * Set encoding for a database connection.
	 *
	 * @param string $encoding
	 * @param resource $connection
	 * @return void
	 */
	public function setCharset($charset, $connection)
	{
		$charset = str_replace('-', '', $charset);
		$sql = "SET NAMES $charset";
		$connection->exec($sql);
	}

	/**
	 * Return the column descriptions for a table.
	 *
	 * @param string $table
	 * @return array
	 */
	public function getFields($table)
	{
		$sql = "DESCRIBE $table";
		$queryResult = $this->query($sql);
		$fields = array();
		foreach ($queryResult['rows'] as $key => $value)
		{
			$fields[$value['Field']]['name'] = $value['Field'];
			$fields[$value['Field']]['type'] = $value['Type'];
			/*
			 * not null is NO or empty, null is YES
			 */
			$fields[$value['Field']]['notnull'] = (bool) ($value['Null'] != 'YES');
			$fields[$value['Field']]['default'] = $value['Default'];
			$fields[$value['Field']]['primary'] = (strtolower($value['Key']) == 'pri');
		}
		return $fields;
	}

	/**
	 * Return the column descriptions for a database (or schema).
	 *
	 * @return array
	 */
	public function getTables()
	{
		$sql = "SHOW TABLES";
		$queryResult = $this->query($sql);
		$tables = array();
		{
			foreach ($queryResult['rows'] as $row)
			{
				$tables[] = $row[key($row)];
			}
		}
		return $tables;
	}
}
