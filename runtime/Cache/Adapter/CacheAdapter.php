<?php
interface LtCacheAdapter
{
	public function connect($hostConf);
	public function add($key, $value, $ttl = 0);
	public function del($key);
	public function get($key);
	public function update($key, $value, $ttl = 0);
}