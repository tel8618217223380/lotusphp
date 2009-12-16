<?php
class LtDbHandler
{
	protected $group;
	protected $node;
	protected $database;
	protected $schema;

	protected $connResource;
	protected $connectionAdapter;
	protected $sqlAdapter;

	public function __construct()
	{
		
	}

	/**
	 * Trancaction methods
	 */
	public function beginTransaction()
	{
		return $this->connResource->beginTransaction();
	}

	public function commit()
	{
		return $this->connResource->commit();
	}
	public function rollBack()
	{
		return $this->connResource->rollBack();
	}

	/**
	 * Connect to db and execute sql query
	 */
	public function connect($connConf)
	{
		return $this->connect($connConf);
	}

	public function exec($sql)
	{
		return $this->connResource->exec($sql);
	}

	public function query($sql, $bind = null, $forceUseMaster = false)
	{
		
		return $this->connResource->query($sql, $bind);
	}

	public function lastInsertId();

	/**
	 * Connection management
	 */
	protected function getConnectionKey($connConf)
	{
		return $connConf['adapter'] . $connConf['host'] . $connConf['port'] . $connConf['username'] . $connConf['dbname'];
	}

	protected function getConnection($role)
	{
		$hosts = LtDbStaticData::$servers[$this->getGroup()][$this->getNode()][$role];
		$connection = false;
		foreach($hosts as $host => $hostConfig)
		{
			$hostConfig = $this->_getConfig($this->getGroup(), $this->getNode(), $role, $host);
			$connectionKey = $this->getConnectionKey($hostConfig);
			if (isset(LtDbStaticData::$connections[$connectionKey]))
			{
				$cachedConnectionInfo = LtDbStaticData::$connections[$connectionKey];
				if (time() < $cachedConnectionInfo['expire_time'])
				{                                        
					$connection = $cachedConnectionInfo['connection'];
					break;
				}
			}
		}
		if (!$connection)
		{
			$hostTotal = count(LtDbStaticData::$servers[$this->getGroup()][$this->getNode()][$role]);
			$hostIndexArray = array_keys(LtDbStaticData::$servers[$this->getGroup()][$this->getNode()][$role]);
			while ($hostTotal)
			{
				$hashNumber = substr(microtime(),7,1) % $hostTotal;
				$hostConfig = $this->_getConfig($this->getGroup(), $this->getNode(), $role, $hostIndexArray[$hashNumber]);
				if ($connection = $this->_connect($hostConfig))
				{
					$connectionKey = $this->getConnectionKey($hostConfig);
					LtDbStaticData::$connections[$connectionKey] = array('connection' => $connection, 'expire_time' => time() + 30);
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
}