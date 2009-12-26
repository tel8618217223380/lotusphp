<?php
interface LtCacheAdapter
{
	public function add($key, $value, $ttl=0, $namespace='');
	public function del($key, $namespace='');
	public function get($key, $namespace='');
	public function update($key, $value, $ttl = 0, $namespace='');
}