<?php
class LtCacheAdapterMemcache implements LtCacheAdapter
{
	public function connect($hostConf)
	{
		return memcache_connect($hostConf["host"], $hostConf["port"]);
	}

	public function add($key, $value, $ttl=0, $tableName, $connectionResource)
	{
		return $connectionResource->add($this->getRealKey($tableName, $key), $value, $ttl);
	}

	public function del($key, $tableName, $connectionResource)
	{
		return $connectionResource->delete($this->getRealKey($tableName, $key));
	}

	public function get($key, $tableName, $connectionResource)
	{
		return $connectionResource->get($this->getRealKey($tableName, $key));
	}

	public function update($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		if ($this->del($this->getRealKey($tableName, $key), $tableName, $connectionResource))
		{
			return $this->add($this->getRealKey($tableName, $key), $value, $ttl, $tableName, $connectionResource);
		}
		else
		{
			return false;
		}
	}

	protected function getRealKey($tableName, $key)
	{
		return $tableName . "-" . $key;
	}
}