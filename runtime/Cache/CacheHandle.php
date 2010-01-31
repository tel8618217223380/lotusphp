<?php
class LtCacheHandle
{
	public $group;
	public $node;
	public $role = "master";
	public $connectionManager;
	protected $connectionAdapter;

	public function __construct()
	{
		$this->connectionManager = new LtCacheConnectionManager;
	}

	public function add($key, $value, $ttl = 0)
	{
		$this->initConnection();
		return $this->connectionAdapter->add($key, $value, $ttl);
	}

	public function del($key)
	{
		$this->initConnection();
		return $this->connectionAdapter->del($key);
	}

	public function get($key)
	{
		$this->initConnection();
		return $this->connectionAdapter->get($key);
	}

	public function update($key, $value, $ttl = 0)
	{
		$this->initConnection();
		return $this->connectionAdapter->update($key, $value, $ttl);
	}

	protected function initConnection()
	{
		$connectionInfo = $this->connectionManager->getConnection($this->group, $this->node, $this->role);
		$this->connectionAdapter = $connectionInfo["connectionAdapter"];
	}
}