<?php
class LtCacheAdapterPhps implements LtCacheAdapter
{
	public $options;

	protected function getCacheFile($key)
	{
		if(!isset($this->options["cache_file_root"]))
		{
			trigger_error("Must set [cache_file_root]");
		}
		$token = md5($key);
		$cachePath = rtrim($this->options["cache_file_root"], '\\/') . DIRECTORY_SEPARATOR
		. substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2);
		if(!is_dir($cachePath))
		{
			if(!@mkdir($cachePath, 0777, true))
			{
				trigger_error("Can not create $cachePath");
			}
		}
		$cacheFile = $cachePath . DIRECTORY_SEPARATOR . 'phps-' . $token. '.php';
		return $cacheFile;
	}

	public function add($key, $value, $ttl=0)
	{
		if(false !== $this->get($key))
		{
			trigger_error("Key Conflict: {$key}");
			return false;
		}
		$cacheFile = $this->getCacheFile($key);
		$expireTime = (0 == $ttl) ? '0000000000' : (time()+$ttl);
		return (boolean) file_put_contents($cacheFile, '<?php exit;?>' . $expireTime . serialize($value));
	}
	
	public function del($key)
	{
		$cacheFile = $this->getCacheFile($key);
		if(!is_file($cacheFile))
		{
			trigger_error("Key not exists: {$key}");
			return false;
		}
		else
		{
			return @unlink($cacheFile);
		}
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
			// php > 5.1.0
			$ttl = file_get_contents($cacheFile,false,null,13,10);
			if(0 != $ttl && time() > $ttl)
			{
				@unlink($cacheFile);
				return false;
			}
			else
			{
				return unserialize(file_get_contents($cacheFile,false,null,23));
			}
		}
	}
}