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
	protected function getConnectionKey($connConf)
	{
		return $connConf['adapter'] . $connConf['host'] . $connConf['port'] . $connConf['username'] . $connConf['dbname'];
	}

	protected function getCachedConnectionInfo($key)
	{
		return isset(LtDbStaticData::$connections[$key]) && time() < LtDbStaticData::$connections[$key]['expire_time']
		? LtDbStaticData::$connections[$key] : false;
	}

	protected function cacheConnection($key, $connectionInfo, $ttl)
	{
		$connectionInfo["expire_time"] = time() + $ttl;
		LtDbStaticData::$connections[$key] = $connectionInfo;
	}

	public function getConnection($group, $node, $role = "master")
	{
		$connectionInfo = null;
		$hosts = LtDbStaticData::$servers[$group][$node][$role];
		foreach($hosts as $host => $hostConfig)
		{
			/**
	 		 * @todo 检查当前schema和charset与用户要操作的目标是否一致
	 		 */
			if($connectionInfo = $this->getCachedConnectionInfo($this->getConnectionKey($hostConfig)))
			{//cached connection resource FOUND
				break;
			}
		}
		if (!$connectionInfo)
		{//no cached connection found
			$hostTotal = count(LtDbStaticData::$servers[$group][$node][$role]);
			$hostIndexArray = array_keys(LtDbStaticData::$servers[$group][$node][$role]);
			while ($hostTotal)
			{
				$hashNumber = substr(microtime(),7,1) % $hostTotal;
				$hostConfig = LtDbStaticData::$servers[$group][$node][$role][$hostIndexArray[$hashNumber]];
				$connectionAdapter = LtDbFactory::getConnectionAdapter($hostConfig["adapter"]);
				$sqlAdapter = LtDbFactory::getSqlAdapter($hostConfig["adapter"]);
				if ($connection = $connectionAdapter->connect($hostConfig))
				{
					$ttl = isset($hostConfig["connection_ttl"]) ? $hostConfig["connection_ttl"] : 30;
					$connectionAdapter->exec($sqlAdapter->setSchema($hostConfig["schema"]));
					$connectionAdapter->exec($sqlAdapter->setCharset($hostConfig["charset"]));
					$connectionInfo = array("connection" => $connection, "schema" => $hostConfig["schema"], "charset" => $hostConfig["charset"]);
					$this->cacheConnection($this->getConnectionKey($hostConfig), $connectionInfo, $ttl);
					break;
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
		}//end if
		return $connectionInfo["connection"];
	}
}