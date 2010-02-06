<?php
class LtCacheAdapterXcache implements LtCacheAdapter
{
	public function connect($hostConf)
	{
		return true;
	}

	public function add($key, $value, $ttl=0, $tableName, $connectionResource)
	{
		return xcache_set($this->getRealKey($tableName, $key), $value, $ttl);
	}
	
	public function del($key, $tableName, $connectionResource)
	{
		return xcache_unset($this->getRealKey($tableName, $key));
	}
	
	public function get($key, $tableName, $connectionResource)
	{
		return xcache_get($this->getRealKey($tableName, $key));
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