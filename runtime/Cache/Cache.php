<?php
class LtCache
{
	public $adapter;

	public function __construct()
	{
		$this->conf = new LtCacheConfig;
	}

	public function init()
	{
		$adapterClassName = "LtCacheAdapter" . ucfirst($this->conf->adapter);
		$this->adapter = new $adapterClassName;
		$this->adapter->options = $this->conf->options;
	}

	public function add($key, $value, $ttl=0)
	{
		return $this->adapter->add($key, $value, $ttl);
	}

	public function del($key)
	{
		return $this->adapter->del($key);
	}

	public function get($key)
	{
		return $this->adapter->get($key);
	}
}