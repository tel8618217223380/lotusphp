<?php
class LtCacheAdapterFile implements LtCacheAdapter
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
		if(false !== $this->get($key))
		{
			trigger_error("Key Conflict: {$key}");
			return false;
		}
		$cacheFile = $this->getCacheFile($key);
		$cachePath = pathinfo($cacheFile,PATHINFO_DIRNAME);
		if(!is_dir($cachePath))
		{
			if(!@mkdir($cachePath, 0777, true))
			{
				trigger_error("Can not create $cachePath");
			}
		}
		$data['ttl'] = (0 >= $ttl) ? 0 : (time()+intval($ttl));
		if(is_object($value) || is_resource($value))
		{
			return (boolean) file_put_contents($cacheFile, "<?php\nreturn array('ttl'=>". $data['ttl'] . ",'value'=>unserialize(\"" . addslashes(serialize($value)) . "\"));\n");
		}
		else
		{
			$data['value'] = $value;
			return (boolean) file_put_contents($cacheFile, "<?php\nreturn ".var_export($data, true).";\n");
		}
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
			// 使用eval而不是eval,防止过多include文件被apc缓存
			$data = file_get_contents($cacheFile,false,null,6);
			$data = eval($data);
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

	public function update($key, $value, $ttl = 0)
	{
		if ($this->del($key))
		{
			return $this->add($key, $value, $ttl);
		}
		else
		{
			return false;
		}
	}

	protected function getCacheFile($key)
	{
		$token = md5($key);
		return $this->cacheFileRoot	. substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .
		DIRECTORY_SEPARATOR . 'file-' . $token. '.php';
	}
}