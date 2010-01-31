<?php
class LtCacheConnectionManager
{
	public $servers;
	protected $connectionAdapter;

	public function getConnection($group, $node, $role)
	{
		if ($connection = $this->getNewConnection($group, $node, $role))
		{
			return array(
				"connectionAdapter" => $this->connectionAdapter,
				"connectionResource" => $connection
			);
		}
		else
		{
			trigger_error("no cache server can be connected");
		}
	}

	protected function getNewConnection($group, $node, $role)
	{
		$servers = $this->servers;
		$hostTotal = count($servers[$group][$node][$role]);
		$hostIndexArray = array_keys($servers[$group][$node][$role]);
		while ($hostTotal)
		{
			$hashNumber = substr(microtime(),7,1) % $hostTotal;
			$hostConfig = $servers[$group][$node][$role][$hostIndexArray[$hashNumber]];
			$cacheFactory = new LtCacheAdapterFactory;
			$this->connectionAdapter = $cacheFactory->getConnectionAdapter($hostConfig["adapter"]);
			if ($connection = $this->connectionAdapter->connect($hostConfig))
			{
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
}