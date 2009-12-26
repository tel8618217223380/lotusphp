<?php
class LtCacheAdapterPhps implements LtCacheAdapter
{
	public $options;

	protected function getCacheFile($key, $namespace='')
	{
		$namespace = $this->getRealKey($namespace, $key);
		$token = md5($key);
		$cachePath = preg_replace('/[\\\\|\/]+/i', '/', $this->options["cache_file_root"]);
		$cachePath = rtrim($cachePath,'\/') . '/';
		$cachePath .= substr($token, 0,2) . '/';
		$cachePath .= substr($token, 2,2);
		if(!is_dir($cachePath))
		{
			if(!mkdir($cachePath, 0777, true))
			{
				trigger_error("Can not create $dir");
			}
		}
		$cachePath = realpath($cachePath);
		$cacheFile = $cachePath . DIRECTORY_SEPARATOR . $namespace . '-phps-' . $token. '.php';
		return $cacheFile;	
	}

	public function add($key, $value, $ttl=0, $namespace='')
	{
		$cacheFile = $this->getCacheFile($key, $namespace);
		$expireTime = (0 == $ttl) ? '0000000000' : (time()+$ttl);
		return file_put_contents($cacheFile, '<?php exit;?>' . $expireTime . serialize($value));
	}
	
	public function del($key, $namespace='')
	{
		$cacheFile = $this->getCacheFile($key, $namespace);
		return unlink($cacheFile);
	}
	
	public function get($key, $namespace='')
	{
		$cacheFile = $this->getCacheFile($key, $namespace);
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
				unlink($cacheFile);
				return false;
			}
			else
			{
				return unserialize(file_get_contents($cacheFile,false,null,23));
			}
		}
	}

	public function update($key, $value, $ttl=0, $namespace='')
	{
		$cacheFile = $this->getCacheFile($key, $namespace);
		$expireTime = (0 == $ttl) ? '0000000000' : (time()+$ttl);
		return file_put_contents($cacheFile, '<?php exit;?>' . $expireTime . serialize($value));
	}

	protected function getRealKey($namespace, $key)
	{
		return sprintf("%u", crc32($namespace)) . $key;
	}
}