<?php
Interface LtStore
{
	public function add($key, $value, $ttl = 0);
	public function del($key);
	public function get($key, $doNotModifiedSince = null);
	public function update($key, $value, $ttl = 0);
}