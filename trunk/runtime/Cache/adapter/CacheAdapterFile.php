<?php
class LtCacheAdapterFile implements LtCacheAdapter
{
	public $options;

	protected function getCacheFile($key, $namespace='')
	{
		//////////echo $this->options["cache_file_root"];
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
		$cacheFile = $cachePath . DIRECTORY_SEPARATOR . $namespace . '-file-' . $token. '.php';
		return $cacheFile;		
	}

	public function add($key, $value, $ttl=0, $namespace='')
	{
		$cacheFile = $this->getCacheFile($key, $namespace);
		$data['ttl'] = (0 >= $ttl) ? 0 : (time()+intval($ttl));
		$data['value'] = $value;
		return file_put_contents($cacheFile, "<?php\nreturn ".var_export($data, true).";\n");
	}
	
	public function del($key, $namespace='')
	{
		$cacheFile = $this->getCacheFile($key, $namespace);
		return @unlink($cacheFile);
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
			$data = include($cacheFile);
			if(0 != $data['ttl'] && time() > $data['ttl'])
			{
				@unlink($cacheFile);
				return false;
			}
			else
			{
				return $data['value'];
			}
		}
	}

	public function update($key, $value, $ttl=0, $namespace='')
	{
		$cacheFile = $this->getCacheFile($key, $namespace);
		$data['ttl'] = (0 >= $ttl) ? 0 : (time()+intval($ttl));
		$data['value'] = $value;
		return file_put_contents($cacheFile, "<?php\nreturn ".var_export($data, true).";\n");
	}

	protected function getRealKey($namespace, $key)
	{
		return sprintf("%u", crc32($namespace)) . $key;
	}
}