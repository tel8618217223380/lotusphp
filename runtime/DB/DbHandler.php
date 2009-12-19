<?php
class LtDbHandler
{
	protected $group;
	protected $node;

	public $connectionAdapter;
	protected $sqlAdapter;

	public function __construct()
	{
		$this->init();
	}

	/**
	 * Trancaction methods
	 */
	public function beginTransaction()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->beginTransaction());
	}

	public function commit()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->commit());
	}

	public function rollBack()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->rollBack());
	}

	/**
	 * Execute an sql query
	 * @param $sql
	 * @param $bind
	 * @param $forceUseMaster
	 * @return false on failed
	 * SELECT, SHOW, DESECRIBE, EXPLAIN return rowset or NULL when no record found
	 * INSERT return the ID generated for an AUTO_INCREMENT column
	 * UPDATE, DELETE return affected count
	 * USE, DROP, ALTER, CREATE, SET etc, return affected count
	 * @todo 如果是读操作，自动去读slave服务器，除非设置了强制读master服务器
	 * @todo 模拟bindParameter，支持占位符
	 */
	public function query($sql, $bind = null, $forceUseMaster = false)
	{
		if(empty($sql))
		{
			// trigger_error('Empty the SQL statement', E_USER_WARNING);
			return null;
		}
		if (is_array($bind))
		{
			$sql = $this->sqlAdapter->bindParameter($sql, $bind);
		}
		if (preg_match("/^\s*SELECT|^\s*EXPLAIN|^\s*SHOW|^\s*DESCRIBE/i", $sql))//read query: SELECT, SHOW, DESCRIBE
		{
			$result = $this->connectionAdapter->query($sql);
			//if (0 === count($result))
			if (empty($result))
			{
				return null;
			}
			else
			{
				return $result;
			}			
		}
		else
		{
			$result = $this->connectionAdapter->exec($sql);
			if (preg_match("/^\s*INSERT/i", $sql))//INSERT
			{
				return $this->connectionAdapter->lastInsertId();
			}
			else if (preg_match("/^\s*UPDATE|^\s*DELETE|^\s*REPLACE/i", $sql))//UPDATE, DELETE, REPLACE
			{
				return $result;
			}
			else//USE, SET, CREATE, DROP, ALTER
			{
				return $result;
			}
		}
	}

	/**
	 * Connection management
	 */
	protected function getConnectionKey($connConf)
	{
		return $connConf['adapter'] . $connConf['host'] . $connConf['port'] . $connConf['username'] . $connConf['dbname'];
	}

	protected function getCachedConnection($key)
	{
		return isset(LtDbStaticData::$connections[$key]) && time() < LtDbStaticData::$connections[$key]['expire_time']
		? LtDbStaticData::$connections[$key]['connection'] : false;
	}

	protected function cacheConnection($key, $connection)
	{
		LtDbStaticData::$connections[$key] = array('connection' => $connection, 'expire_time' => time() + 30);
	}


	/**
	 * Get group, node
	 */
	protected function getGroup()
	{
		if (1 == count(LtDbStaticData::$servers))
		{
			$this->group = key(LtDbStaticData::$servers);
		}
		return $this->group;
	}

	protected function getNode()
	{
		if (NULL === $this->node)
		{
			$nodeArray = array_keys(LtDbStaticData::$servers[$this->getGroup()]);
			if (1 === count($nodeArray))
			{
				$this->node = $nodeArray[0];
			}
			else
			{
				DebugHelper::debug('DB_NODE_NOT_SPECIFIED', array('group' => $this->getGroup()));
			}
		}
		return $this->node;
	}

	/**
	 * Get db config
	 */
	protected function getConfig($group, $node, $role = 'master', $host = null)
	{
		$nodeArray = array_keys(LtDbStaticData::$servers[$group]);
		$hostArray = array_keys(LtDbStaticData::$servers[$group][$node][$role]);
		if (!$host)
		{
			$host = $hostArray[0];
			$config = LtDbStaticData::$servers[$group][$node][$role][$host];
		}
		else
		{
			$config = array_merge(
			LtDbStaticData::$servers[$group][$node][$role][$hostArray[0]],
			LtDbStaticData::$servers[$group][$node][$role][$host]
			);
		}
		if ('slave' == $role)
		{
			$masterIndexArray = array_keys(LtDbStaticData::$servers[$group][$node]['master']);
			$config = array_merge(
			LtDbStaticData::$servers[$group][$nodeArray[0]]['master'][$masterIndexArray[0]],
			$config
			);
		}
		$firstNodeHostIndexArray = array_keys(LtDbStaticData::$servers[$group][$nodeArray[0]][$role]);
		$config = array_merge(
		LtDbStaticData::$servers[$group][$nodeArray[0]][$role][$firstNodeHostIndexArray[0]],
		$config
		);
		if (!isset($config["schema"]) || empty($config["schema"]))
		{
			$config["schema"] = $config["dbname"];
		}
		return $config;
	}

	/**
	 * Init connection
	 * @todo 连接失败处理
	 */
	protected function init($role = "master")
	{
		$hosts = LtDbStaticData::$servers[$this->getGroup()][$this->getNode()][$role];
		$connection = false;
		foreach($hosts as $host => $hostConfig)
		{
			$hostConfig = $this->getConfig($this->getGroup(), $this->getNode(), $role, $host);
			if($connection = $this->getCachedConnection($this->getConnectionKey($hostConfig)))
			{
				$this->initAdapter($hostConfig);
				break;
			}
		}
		if (!$connection)
		{
			$hostTotal = count(LtDbStaticData::$servers[$this->getGroup()][$this->getNode()][$role]);
			$hostIndexArray = array_keys(LtDbStaticData::$servers[$this->getGroup()][$this->getNode()][$role]);
			while ($hostTotal)
			{
				$hashNumber = substr(microtime(),7,1) % $hostTotal;
				$hostConfig = $this->getConfig($this->getGroup(), $this->getNode(), $role, $hostIndexArray[$hashNumber]);
				$this->initAdapter($hostConfig);
				if ($connection = $this->connectionAdapter->connect($hostConfig))
				{
					$this->cacheConnection($this->getConnectionKey($hostConfig), $connection);
					break;
				}
				else
				{
					trigger_error('connection fail', E_USER_WARNING);
				}
				for ($i = $hashNumber; $i < $hostTotal - 1; $i ++)
				{
					$hostIndexArray[$i] = $hostIndexArray[$i+1];
				}
				unset($hostIndexArray[$hostTotal-1]);
				$hostTotal --;
			}
		}
		$this->connectionAdapter->connResource = $connection;
		$this->query($this->sqlAdapter->setSchema($hostConfig["schema"]));
		$this->query($this->sqlAdapter->setCharset($hostConfig["charset"]));
	}

	/**
	 * Init adapter instances
	 */
	protected function initAdapter($hostConfig)
	{
		if (preg_match("/^pdo_/i", $hostConfig["adapter"]))
		{
			$LtDbSqlAdapter = "LtDbSqlAdapter" . ucfirst(substr($hostConfig["adapter"], 4));
			$LtDbConnectionAdapter = "LtDbConnectionAdapterPdo";
		}
		else
		{
			$LtDbSqlAdapter = "LtDbSqlAdapter" . ucfirst($hostConfig["adapter"]);
			$LtDbConnectionAdapter = "LtDbConnectionAdapter" . ucfirst($hostConfig["adapter"]);
		}
		/**
		 * Mysqli use mysql syntax
		 */
		if ("mysqli" == $hostConfig["adapter"])
		{
			$LtDbSqlAdapter = "LtDbSqlAdapterMysql";
		}
		$this->sqlAdapter = new $LtDbSqlAdapter();
		$this->connectionAdapter = new $LtDbConnectionAdapter();
	}
}