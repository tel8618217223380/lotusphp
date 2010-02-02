<?php
class LtCacheAdapterPhps implements LtCacheAdapter
{
	protected $cacheFileRoot;

	public function connect($hostConf)
	{	
		if(isset($hostConf["host"]))
		{
			$this->cacheFileRoot = rtrim($hostConf["host"], '\\/') . DIRECTORY_SEPARATOR;
			if (isset($hotConf["key_prefix"]))
			{
				$this->cacheFileRoot .= $hotConf["key_prefix"] . DIRECTORY_SEPARATOR;
				
			}
			return true;
		}
		else
		{
			trigger_error("Must set [host]");
		}
	}

	public function add($key, $value, $ttl=0)
	{
		$cacheFile = $this->getCacheFile($key);
		if(is_file($cacheFile))
		{
			trigger_error("Key Conflict: {$key}");
			return false;
		}
		$cachePath = pathinfo($cacheFile,PATHINFO_DIRNAME);
		if(!is_dir($cachePath))
		{
			if(!@mkdir($cachePath, 0777, true))
			{
				trigger_error("Can not create $cachePath");
			}
		}
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
			return unlink($cacheFile);
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
				unlink($cacheFile);
				return false;
			}
			else
			{
				return unserialize(file_get_contents($cacheFile,false,null,23));
			}
		}
	}

	public function update($key, $value, $ttl = 0)
	{
		$cacheFile = $this->getCacheFile($key);
		if(!is_file($cacheFile))
		{
			trigger_error("Key not exists: {$key}");
			return false;
		}
		$expireTime = (0 == $ttl) ? '0000000000' : (time()+$ttl);
		return (boolean) file_put_contents($cacheFile, '<?php exit;?>' . $expireTime . serialize($value));
	}

	protected function getCacheFile($key)
	{
		$token = md5($key);
		return $this->cacheFileRoot	. substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .
		DIRECTORY_SEPARATOR . 'phps-' . $token. '.php';
	}
}