<?php
class LtMultiDb
{
	public $storeHandle;

	protected $group;
	protected $node;

	/**
	 * Connection management
	 * @todo 连接缓存将database, schema, resource id, expire_time都保存，当setSchema(), USE DB, SET search_path to schame的时候，更新连接缓存
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

	protected function cacheConnection($key, $connection, $ttl)
	{
		LtDbStaticData::$connections[$key] = array('connection' => $connection, 'expire_time' => time() + $ttl);
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
	 * @todo mysql, mssql, firebird, maxdb数据库的database应该视为schema，多个schema对应一个连接；oracle, pgsql的DB是真正的DB，一个DB对应一个连接 
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
	public function init($role = "master")
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
					$ttl = isset($hostConfig["connection_ttl"]) ? $hostConfig["connection_ttl"] : 30;
					$this->cacheConnection($this->getConnectionKey($hostConfig), $connection, $ttl);
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