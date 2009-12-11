<?php
class LtCacheAdapterPhps extends LtCacheAdapter
{
	protected $cacheFileRoot;

	protected function getCacheFile($key)
	{
		if (null == $this->cacheFileRoot)
		{
			$this->cacheFileRoot = $this->options["cache_file_root"] . DIRECTORY_SEPARATOR;
		}
		$token = md5($key);
		return $this->cacheFileRoot . substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .  DIRECTORY_SEPARATOR . $token;
	}

	public function add($key, $value, $ttl=0)
	{
		$cacheFile = $this->getCacheFile($key);
		if (is_file($cacheFile))
		{
			return false;
		}
		$cacheDir = dirname($cacheFile);
		if (!is_dir($cacheDir))
		{
			mkdir($cacheDir, 0777, true);
		}
		return file_put_contents($cacheFile, serialize($value));
	}
	
	public function del($key)
	{
		$cacheFile = $this->getCacheFile($key);
		return unlink($cacheFile);
	}
	
	public function get($key)
	{
		$cacheFile = $this->getCacheFile($key);
		if (!is_file($cacheFile))
		{
			return false;
		}
		else
		{
			return unserialize(file_get_contents($cacheFile));
		}
	}
}