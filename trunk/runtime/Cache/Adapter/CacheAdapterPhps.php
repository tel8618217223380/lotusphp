<?php
class LtCacheAdapterPhps implements LtCacheAdapter
{
	public function connect($hostConf)
	{
		$fileStore = new LtStoreFile;
		$fileStore->prefix = 'LtCache-phps-';
		$fileStore->useSerialize = true;
		$fileStore->init();
		return $fileStore;
	}

	public function add($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		return $connectionResource->add($this->getRealKey($tableName, $key), $this->valueToString($value), $ttl);
	}

	public function del($key, $tableName, $connectionResource)
	{
		return $connectionResource->del($this->getRealKey($tableName, $key));
	}

	public function get($key, $tableName, $connectionResource)
	{
		return $this->stringToValue($connectionResource->get($this->getRealKey($tableName, $key)));
	}

	public function update($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		return $connectionResource->update($this->getRealKey($tableName, $key), $this->valueToString($value), $ttl);
	}

	protected function getRealKey($tableName, $key)
	{
		return $tableName . "-" . $key;
	}

	protected function valueToString($value)
	{
		return serialize($value);
	}

	protected function stringToValue($str)
	{
		return unserialize($str);
	}
}
