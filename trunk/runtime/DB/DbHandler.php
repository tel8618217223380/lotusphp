<?php
class LtDbHandler
{
	protected $group;
	protected $node;

	protected $connectionAdapter;
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
		return $$this->connectionAdapter->beginTransaction();
	}

	public function commit()
	{
		return $this->connectionAdapter->commit();
	}
	public function rollBack()
	{
		return $this->connectionAdapter->rollBack();
	}

	/**
	 * Connect to db and execute sql query
	 */
	public function exec($sql)
	{
		return $this->connectionAdapter->exec($sql);
	}

	public function query($sql, $bind = null, $forceUseMaster = false)
	{
		return $this->connectionAdapter->query($sql, $bind);
	}

	public function lastInsertId()
	{
		return $this->connectionAdapter->lastInsertId($sql);
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
	 * Get node, group, schema
	 */
	public function getNode()
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

	public function getGroup()
	{
		if (1 == count(LtDbStaticData::$servers))
		{
			$this->group = key(LtDbStaticData::$servers);
		}
		return $this->group;
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
				for ($i = $hashNumber; $i < $hostTotal - 1; $i ++)
				{
					$hostIndexArray[$i] = $hostIndexArray[$i+1];
				}
				unset($hostIndexArray[$hostTotal-1]);
				$hostTotal --;
			}
		}
		$this->connectionAdapter->connResource = $connection;
		$this->exec($this->sqlAdapter->setSchema($hostConfig["schema"]));
	}

	/**
	 * Init adapter instances
	 */
	protected function initAdapter($hostConfig)
	{
		switch ($hostConfig["adapter"])
		{
			case "mysql":
				$this->sqlAdapter = new LtDbSqlAdapterMysql();
				$this->connectionAdapter = new LtDbConnectionAdapterMysql();
				break;
			case "mysqli":
				$this->sqlAdapter = new LtDbSqlAdapterMysql();
				$this->connectionAdapter = new LtDbConnectionAdapterMysqli();
				break;
			case "pdo_mysql":
				$this->sqlAdapter = new LtDbSqlAdapterMysql();
				$this->connectionAdapter = new LtDbConnectionAdapterPdo();
				break;
		}		
	}
}