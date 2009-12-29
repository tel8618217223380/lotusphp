<?php
class LtDbConnectionManager
{
	/**
	 * Connection management
	 * array(
	 * 	"connection"  => connection resource id,
	 * 	"expire_time" => expire time,
	 * 	"schema"      => default schema name,
	 * 	"charset"     => char set / encoding
	 * )
	 */
	protected $connectionAdapter;
	protected $sqlAdapter;

	protected function getConnectionKey($connConf)
	{
		return $connConf['adapter'] . $connConf['host'] . $connConf['port'] . $connConf['username'] . $connConf['dbname'];
	}

	protected function saveConnection($connConf, $connection, $ttl)
	{
		$connectionInfo = array(
			"connection"  => $connection,
			"expire_time" => time() + $ttl,
			"schema"      => $connConf["schema"],
			"charset"     => $connConf["charset"],
		);
		LtDbStaticData::$connections[$this->getConnectionKey($connConf)] = $connectionInfo;
	}

	protected function getCachedConnection($group, $node, $role)
	{
		foreach(LtDbStaticData::$servers[$group][$node][$role] as $hostConfig)
		{
			$key = $this->getConnectionKey($hostConfig);
			if(isset(LtDbStaticData::$connections[$key]) && time() < LtDbStaticData::$connections[$key]['expire_time'])
			{//cached connection resource FOUND
				$connectionInfo = LtDbStaticData::$connections[$key];
				if ($connectionInfo["schema"] != $hostConfig["schema"] || $connectionInfo["charset"] != $hostConfig["charset"])
				{//检查当前schema和charset与用户要操作的目标不一致
					$hostConfig = LtDbStaticData::$servers[$group][$node][$role][$hostIndexArray[$hashNumber]];
					$dbFactory = new LtDbFactory;
					$this->connectionAdapter = $dbFactory->getConnectionAdapter($hostConfig["adapter"]);
					$this->sqlAdapter = $dbFactory->getSqlAdapter($hostConfig["adapter"]);
					$this->connectionAdapter->connResource = $connectionInfo["connection"];
					if ($connectionInfo["schema"] != $hostConfig["schema"])
					{
						$this->connectionAdapter->exec($this->sqlAdapter->setSchema($hostConfig["schema"]));
					}
					if ($connectionInfo["charset"] != $hostConfig["charset"])
					{
						$this->connectionAdapter->exec($this->sqlAdapter->setCharset($hostConfig["charset"]));
					}
					$this->saveConnection($hostConfig, $connectionInfo["connection"], $hostConfig["connection_ttl"]);
				}
				return $connectionInfo["connection"];
			}
		}
		return false;
	}

	protected function getNewConnection($group, $node, $role)
	{
		$hostTotal = count(LtDbStaticData::$servers[$group][$node][$role]);
		$hostIndexArray = array_keys(LtDbStaticData::$servers[$group][$node][$role]);
		while ($hostTotal)
		{
			$hashNumber = substr(microtime(),7,1) % $hostTotal;
			$hostConfig = LtDbStaticData::$servers[$group][$node][$role][$hostIndexArray[$hashNumber]];
			$dbFactory = new LtDbFactory;
			$this->connectionAdapter = $dbFactory->getConnectionAdapter($hostConfig["adapter"]);
			$this->sqlAdapter = $dbFactory->getSqlAdapter($hostConfig["adapter"]);
			if ($connection = $this->connectionAdapter->connect($hostConfig))
			{
				$this->connectionAdapter->connResource = $connection;
				$this->connectionAdapter->exec($this->sqlAdapter->setSchema($hostConfig["schema"]));
				$this->connectionAdapter->exec($this->sqlAdapter->setCharset($hostConfig["charset"]));
				$this->saveConnection($hostConfig, $connection, $hostConfig["connection_ttl"]);
				return $connection;
			}
			else
			{
				//trigger_error('connection fail', E_USER_WARNING);
				//delete the unavailable server
				for ($i = $hashNumber; $i < $hostTotal - 1; $i ++)
				{
					$hostIndexArray[$i] = $hostIndexArray[$i+1];
				}
				unset($hostIndexArray[$hostTotal-1]);
				$hostTotal --;
			}//end else
		}//end while
		return false;
	}

	public function getAdapters($group, $node, $role = "master")
	{
		if ($connection = $this->getNewConnection($group, $node, $role) || $connection = $this->getNewConnection($group, $node, $role))
		{
			return array(
				"connectionAdapter" => $this->connectionAdapter,
				"sqlAdapter" => $this->sqlAdapter
			);
		}
		else
		{
			trigger_error("no db server can be connected");
		}
	}
}