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
		$token = md5($key);
		$cachePath = rtrim($this->options["cache_file_root"], '\\/') . DIRECTORY_SEPARATOR
		. substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2);
		if(!is_dir($cachePath))
		{
			if(!mkdir($cachePath, 0777, true))
			{
				trigger_error("Can not create $cachePath");
			}
		}
		$cacheFile = $cachePath . DIRECTORY_SEPARATOR . 'file-' . $token. '.php';
		return $cacheFile;
	}

	public function add($key, $value, $ttl=0)
	{
		$cacheFile = $this->getCacheFile($key);
		$data['ttl'] = (0 >= $ttl) ? 0 : (time()+intval($ttl));
		if(is_object($value) || is_resource($value))
		{
			$str  = "<?php\n";
			$str .= '$obj = unserialize("' . addslashes(serialize($value)) . '");'."\n";
			$str .= 'return array(\'ttl\' => '. $data['ttl'] . ', \'value\' => $obj,);'."\n";
			return (boolean) file_put_contents($cacheFile, $str);
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
		return @unlink($cacheFile);
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