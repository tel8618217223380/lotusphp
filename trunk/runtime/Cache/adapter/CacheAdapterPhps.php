<?php
class LtCacheAdapterPhps extends LtCacheAdapter
{
	protected function getCacheFile($key)
	{
		$this->options["cache_file_root"] = preg_match("/[\\|\/]$/", $this->options["cache_file_root"]) ? $this->options["cache_file_root"] : $this->options["cache_file_root"] . DIRECTORY_SEPARATOR;
		$token = md5($key);
		return $this->options["cache_file_root"] . substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .  DIRECTORY_SEPARATOR . 'Lotusphp-cache-' . $token . '.php';
	}

	public function add($key, $value, $ttl=0)
	{
		$cacheFile = $this->getCacheFile($key);
		$cacheDir = dirname($cacheFile);
		if (!is_dir($cacheDir))
		{
			mkdir($cacheDir, 0777, true);
		}
		$expireTime = (0 == $ttl) ? '0000000000' : (time()+$ttl);
		return file_put_contents($cacheFile, '<?php exit;?>' . $expireTime . serialize($value));
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
		{	//5.0.0 ����˶� context ��֧�֡�  
			//5.1.0 ����� offset �� maxlen ���� 
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
}