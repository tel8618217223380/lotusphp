<?php
class LtDbHandler
{
	protected $group;
	protected $node;
	protected $database;
	protected $schema;

	/**
	 * Trancaction methods
	 */
	public function beginTransaction()
	{
		
	}
	public function commit();
	public function rollBack();

	/**
	 * Connect to db and execute sql query
	 */
	public function connect($connConf);
	public function exec($sql);
	public function query($sql, $bind = null, $forceUseMaster = false);
	public function lastInsertId();
	/**
	 * Connection management
	 */
	protected function getConnection($connConf)
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
			$connectionKey = self::_getConnectionKey($hostConfig);
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
					$connectionKey = self::_getConnectionKey($hostConfig);
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