<?php
/**
var_export不支持object
参考: http://php.net/manual/en/function.var-export.php
serialize支持object
*/
class LtCacheAdapterFile implements LtCacheAdapter
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
		$cacheFile = $cachePath . DIRECTORY_SEPARATOR . 'file-' . $token. '.php';
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
			// ----------------------------
			// $data = include($cacheFile);
			// -----------------------------
			// 防止过多include文件被记录到php引擎
			// 通过查看get_included_files()可知
			// 同时防止过多include文件被apc缓存
			// ------------------------------------------------
			$data = file_get_contents($cacheFile,false,null,6);
			$data = eval($data);
			// ------------------------------------------------
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
}