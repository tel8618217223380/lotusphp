<?php
class LtCacheAdapterFile implements LtCacheAdapter
{
	public function connect($hostConf)
	{
		$fileStore = new LtStoreFile;
		if (isset($hostConf['host']) && is_string($hostConf['host']))
		{
			$fileStore->setFileRoot($hostConf['host'], 'Ltcache-file-');
			return $fileStore;
		}
		else
		{
			trigger_error("Must set [host]");
			return false;
		}
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
		if (is_object($value) || is_resource($value))
		{
			$str = "return unserialize(\"" . addslashes(serialize($value)) . "\");";
		}
		else
		{
			$str = "return " . var_export($value, true) . ";";
		}
		return $str;
	}

	protected function stringToValue($str)
	{
		return eval("$str");
	}
}
