<?php
class LtCacheFile
{
	public $options;

	public function __construct()
	{
		$this->options['cache_file_root'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmp/ltCache';
	
	}
	protected function getCacheFile($key, $namespace='LtCacheFile')
	{
		$namespace = $this->getRealKey($namespace, $key);
		$dir = $this->options["cache_file_root"];
		//realpath在目录不存在时结果为空
		$dir = str_replace('\\', '/', $dir);
		$dir = rtrim($dir, '\\/');
		if(!is_dir($dir))
		{
			if(!mkdir($dir, 0777, true))
			{
				trigger_error("Can not create $dir");
			}
		}
		$dir = realpath($dir);
		$dir = rtrim($dir, '\\/');
		$token = md5($key);
		return $dir . DIRECTORY_SEPARATOR . substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .  DIRECTORY_SEPARATOR . "$namespace-file-$token.php";
	}

	public function add($key, $value, $ttl=0, $namespace='LtCacheFile')
	{
		$cacheFile = $this->getCacheFile($key);
		//echo $cacheFile;
		$cacheDir = dirname($cacheFile);
		//echo 'chdir=' . $cacheDir;
		if (!is_dir($cacheDir))
		{
			if(!mkdir($cacheDir, 0777, true))
			{
				trigger_error("Can not create $dir");
			}
		}
		$data['ttl'] = (0 >= $ttl) ? 0 : (time()+intval($ttl));
		$data['value'] = $value;
		return file_put_contents($cacheFile, "<?php\nreturn ".var_export($data, true).";\n");
	}
	
	public function del($key, $namespace='LtCacheFile')
	{
		$cacheFile = $this->getCacheFile($key);
		return unlink($cacheFile);
	}
	
	public function get($key, $namespace='LtCacheFile')
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

	public function update($key, $value, $ttl=0, $namespace='LtCacheFile')
	{
		//$this->stack[$this->getRealKey($namespace, $key)] = $value;
		//return true;
		$cacheFile = $this->getCacheFile($key);
		$cacheDir = dirname($cacheFile);
		if (!is_dir($cacheDir))
		{
			mkdir($cacheDir, 0777, true);
		}
		$data['ttl'] = (0 >= $ttl) ? 0 : (time()+intval($ttl));
		$data['value'] = $value;
		return file_put_contents($cacheFile, "<?php\nreturn ".var_export($data, true).";\n");
	}

	protected function getRealKey($namespace, $key)
	{
		return sprintf("%u", crc32($namespace)) . $key;
	}

}