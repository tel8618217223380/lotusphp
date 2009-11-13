<?php
class LtCache
{
	public $adapter;

	public function init($adapter, $options = array())
	{
		$adapterClassName = "LtCacheAdapter" . ucfirst($adapter);
		$this->adapter = new $adapterClassName;
	}
	
	public function add($key, $value)
	{
		return $this->adapter->add($key, $value);
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