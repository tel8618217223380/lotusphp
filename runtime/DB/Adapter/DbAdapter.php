<?php
/**
 * Database adapter class
 *
 * @todo cache config in opcode cache, avoid getConfig() when query
 */
abstract class DbAdapter
{
	/**
	 * The current connection
	 *
	 * @var resource
	 */
	protected $_connection;

	/**
	 * Fetch mode
	 *
	 * @var integer
	 */
	protected $_fetchMode = PDO::FETCH_ASSOC;

	/**
	 * The group name
	 *
	 * @var string
	 */
	protected $_group;

	/**
	 * The hash node index
	 *
	 * @var string
	 */
	protected $_node;

	/**
	 * The schema name
	 *
	 * @var string
	 */
	protected $_schema;

	/**
	 * The prepared statement
	 *
	 * @var object
	 */
	protected $_statement;

	/**
	 * Initiate a transaction
	 *
	 * @return boolean
	 */
	abstract protected function _beginTransaction();

	/**
	 * Connects to the database.
	 *
	 * @param array $config
	 * @return void
	 * @throws Exception
	 */
	abstract protected function _connect($config);

	/**
	 * Get current db configuration
	 *
	 * @param string $group
	 * @param string $node
	 * @param string $role
	 * @param string $host
	 * @return array
	 */
	protected function _getBasicConfig($group, $node, $role = 'master', $host = null)
	{
		$nodeArray = array_keys(Config::$app["db_server"][$group]);
		$hostArray = array_keys(Config::$app["db_server"][$group][$node][$role]);
		if (!$host)
		{
			$host = $hostArray[0];
			$config = Config::$app["db_server"][$group][$node][$role][$host];
		}
		else
		{
			$config = array_merge(
			Config::$app["db_server"][$group][$node][$role][$hostArray[0]],
			Config::$app["db_server"][$group][$node][$role][$host]
			);
		}
		if ('slave' == $role)
		{
			$masterIndexArray = array_keys(Config::$app["db_server"][$group][$node]['master']);
			$config = array_merge(
			Config::$app["db_server"][$group][$nodeArray[0]]['master'][$masterIndexArray[0]],
			$config
			);
		}
		$firstNodeHostIndexArray = array_keys(Config::$app["db_server"][$group][$nodeArray[0]][$role]);
		$config = array_merge(
		$this->_config,
		Db::$defaultConfig,
		Config::$app["db_server"][$group][$nodeArray[0]][$role][$firstNodeHostIndexArray[0]],
		$config
		);
		return $config;
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
	abstract protected function _getConfig($group, $node, $role = 'master', $host = null);

	/**
	 * Get connection by config, created connection if needed
	 *
	 * @param array $config
	 * @return resource
	 */
	protected function _getConnection($role)
	{
		$hosts = Config::$app["db_server"][$this->getGroup()][$this->getNode()][$role];
		$connection = false;
		foreach($hosts as $host => $hostConfig)
		{
			$hostConfig = $this->_getConfig($this->getGroup(), $this->getNode(), $role, $host);
			$connectionKey = self::_getConnectionKey($hostConfig);
			if (isset(Db::$connections[$connectionKey]))
			{
				$cachedConnectionInfo = Db::$connections[$connectionKey];
				if (time() < $cachedConnectionInfo['expire_time'])
				{                                        
					$connection = $cachedConnectionInfo['connection'];
					break;
				}
			}
		}
		if (!$connection)
		{
			$hostTotal = count(Config::$app["db_server"][$this->getGroup()][$this->getNode()][$role]);
			$hostIndexArray = array_keys(Config::$app["db_server"][$this->getGroup()][$this->getNode()][$role]);
			while ($hostTotal)
			{
				$hashNumber = substr(microtime(),7,1) % $hostTotal;
				$hostConfig = $this->_getConfig($this->getGroup(), $this->getNode(), $role, $hostIndexArray[$hashNumber]);
				if ($connection = $this->_connect($hostConfig))
				{
					$connectionKey = self::_getConnectionKey($hostConfig);
					Db::$connections[$connectionKey] = array('connection' => $connection, 'expire_time' => time() + 30);
					break;
				}
				for ($i = $hashNumber; $i < $hostTotal - 1; $i ++)
				{
					$hostIndexArray[$i] = $hostIndexArray[$i+1];
				}
				unset($hostIndexArray[$hostTotal-1]);
				$hostTotal --;
			}
		}
		return $connection;
	}

	/**
	 * Generate unique array key for connection
	 *
	 * @param array $config
	 * @return string
	 */
	static protected function _getConnectionKey($config)
	{
		return $config['host'] . $config['port'] . $config['username'] . $config['dbname'];
	}

	/**
	 * Change current schema
	 *
	 * @return void
	 */
	abstract protected function _useSchema();

	/**
	 * Connect to database and initiate a transaction
	 *
	 * @return boolean
	 */
	public function beginTransaction()
	{
		$this->_connection = $this->_getConnection('master');
		return $this->_beginTransaction();
	}

	/**
	 * Commit a transaction
	 *
	 * @return boolean
	 */
	abstract public function commit();

	/**
	 * Get current node index
	 *
	 * @return string
	 */
	public function getNode()
	{
		//~ fix issue 11
		if (NULL === $this->_node)
		{
			$nodeArray = array_keys(Config::$app["db_server"][$this->getGroup()]);
			if (1 === count($nodeArray))
			{
				$this->_node = $nodeArray[0];
			}
			else
			{
				DebugHelper::debug('DB_NODE_NOT_SPECIFIED', array('group' => $this->getGroup()));
			}
		}
		return $this->_node;
	}

	/**
	 * Get current schema
	 *
	 * @return string
	 */
	public function getSchema()
	{
		return $this->_schema;
	}

	/**
	 * Get the table's group name
	 *
	 * @return string
	 */
	public function getGroup()
	{
		return $this->_group;
	}

	/**
	 * Get the last inserted ID.
	 *
	 * @param string $tableName
	 * @param string $primaryKey
	 * @return integer
	 */
	abstract public function lastInsertId($tableName = null, $primaryKey = null);

	/**
	 * Add an adapter-specific LIMIT clause to the SELECT statement
	 *
	 * @param string $sql
	 * @param integer $limit
	 * @param integer $offset
	 * @return string
	 */
	abstract public function limit($sql, $limit, $offset);

	/**
	 * Prepare a statement and return a PDOStatement object.
	 *
	 * @param string $sql
	 * @return PDOStatement
	 */
	abstract public function prepare($sql);

	/**
	 * Prepare and execute an SQL statement with bound data.
	 *
	 * @param string $sql
	 * @param array $bind
	 * @return array('rows' => $recordSet, 'row_total' => $rowTotal)
	 * @example query('SHOW TABLES', null, true);
	 */
	public function query($sql, $bind = null, $useSlave = false)
	{
		$connection = null;
		if ($useSlave && isset(Config::$app["db_server"][$this->getGroup()][$this->getNode()]['slave']))
		{
			$connection = $this->_getConnection('slave');
		}
		else
		{
			$connection = $this->_getConnection('master');
		}
		if (!$connection)
		{
			if(function_exists('onDbConnectFailed'))
			{
				call_user_func('onDbConnectFailed');
			}
			else
			{
				throw new Exception("No database server can be connected.");
			}
		}
		$this->_connection = $connection;
		$this->_useSchema();
		$this->_statement = $this->prepare($sql);
		$this->_statement->execute((array) $bind);
		$this->_statement->setFetchMode($this->_fetchMode);
		if ('00000' != $this->_statement->errorCode())
		{
			if(function_exists('onDbQueryFailed'))
			{
				call_user_func('onDbQueryFailed', $this->_statement, $sql, $bind);
			}
			else
			{
				$errorInfo = $this->_statement->errorInfo();
				throw new Exception('SQL query exception (code: [' . $this->_statement->errorCode() . ']; sql: [' . $sql . ']; message:' . $errorInfo[2]);
			}
		}
		$data['row_total'] = $this->_statement->rowCount();
		$data['rows'] = $this->_statement->fetchAll();
		$this->_statement->closeCursor();
		return $data;
	}

	/**
	 * Roll back a transaction
	 *
	 * @return boolean
	 */
	abstract public function rollBack();

	/**
	 * Set encoding for a database connection.
	 *
	 * @param resource $connection
	 * @param string $encoding
	 * @return void
	 */
	abstract public function setCharset($charset, $connection);

	/**
	 * Set current node index
	 *
	 * @param string $node
	 * @return void
	 * @example setNode('a');
	 */
	public function setNode($node)
	{
		if (isset(Config::$app["db_server"][$this->getGroup()][$node]))
		{
			$this->_node = $node;
		}
		else
		{
			DebugHelper::debug('DB_NODE_NOT_FOUND', array('group' => $this->getGroup(), 'node' => $node));
		}
	}

	/**
	 * Set group name
	 *
	 * @param string $group
	 * @return void
	 * @example setGroup('global_farm');
	 */
	public function setGroup($group)
	{
		if (isset(Config::$app["db_server"][$group]))
		{
			$this->_group = $group;
		}
		else
		{
			DebugHelper::debug('DB_GROUP_NOT_FOUND', array('group' => $group));
		}
	}



	/**
	 * Set current schema
	 *
	 * @param string $schema
	 * @return void
	 * @example setSchema('public');
	 */
	public function setSchema($schema)
	{
		$this->_schema = $schema;
	}

	/**
	 * Return the column descriptions for a table.
	 *
	 * @param string $table
	 * @return array
	 */
	abstract public function showFields($table);
}
