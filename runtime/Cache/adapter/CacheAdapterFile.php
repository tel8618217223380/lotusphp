<?php
class LtCacheAdapterPhps extends LtCacheAdapter
{
	public function __construct()
	{
		$this->options["cache_file_root"] = rtrim($this->options["cache_file_root"],'\/') . DIRECTORY_SEPARATOR;
	}

	protected function getCacheFile($key)
	{
		$token = md5($key);
		return $this->options["cache_file_root"] . substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .  DIRECTORY_SEPARATOR . "key-$token.php";
	}

	public function add($key, $value, $ttl=0)
	{
		$cacheFile = $this->getCacheFile($key);
		$cacheDir = dirname($cacheFile);
		if (!is_dir($cacheDir))
		{
			mkdir($cacheDir, 0777, true);
		}
		$data['ttl'] = (0 > $ttl) ? 0 : (time()+intval($ttl));
		$data['value'] = $value;
		return file_put_contents($cacheFile, "<?php\nreturn ".var_export($data, true).";\n");
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
			$data = include($cacheFile);
			if(0 != $data['ttl'] && time() > $data['ttl'])
			{
				unlink($cacheFile);
				return false;
			}
			else
			{
				return $data['value'];
			}
		}
	}
}