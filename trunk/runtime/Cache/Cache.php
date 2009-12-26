<?php
class LtCache
{
	protected $cacheHandle;

	public $namespaceMapping;

	public function __construct()
	{
		$this->conf = new LtCacheConfig;
	}

	public function init()
	{
		$adapterClassName = "LtCacheAdapter" . ucfirst($this->conf->adapter);
		$this->cacheHandle = new $adapterClassName;
		if (property_exists($this->cacheHandle, "options"))
		{
			$this->cacheHandle->options = $this->conf->options;
		}
	}

	public function add($key, $value, $ttl = 0, $namespace='')
	{
		return $this->cacheHandle->add($this->getRealKey($namespace, $key), $value, $ttl);
	}

	public function del($key, $namespace='')
	{
		return $this->cacheHandle->del($this->getRealKey($namespace, $key));
	}

	public function get($key, $namespace='')
	{
		return $this->cacheHandle->get($this->getRealKey($namespace, $key));
	}
	/**
	* @todo ÊÇ·ñ¿¼ÂÇºÏ²¢add update
	*/
	public function update($key, $value, $ttl = 0, $namespace='')
	{
		$realKey = $this->getRealKey($namespace, $key);
		//if ($result = $this->cacheHandle->del($realKey))
		//{
			return $this->cacheHandle->add($realKey, $value, $ttl);
		//}
		//return $result;
	}

	protected function getRealKey($namespace, $key)
	{
		$keyPrefix = "";
		if ($this->namespaceMapping)
		{
			if (isset($this->namespaceMapping[$namespace]))
			{
				$keyPrefix = $this->namespaceMapping[$namespace];
			}
			else
			{
				trigger_error("Invalid namespace: $namespace, please register it first");
			}
		}
		else
		{
			$keyPrefix = sprintf("%u", crc32($namespace));
		}
		return $keyPrefix.$key;
	}
}