<?php
class LtCacheAdapterPhps implements LtCacheAdapter
{
	protected $cacheFileRoot;

	public function connect($hostConf)
	{
		if (isset($hostConf['host']) && is_string($hostConf['host']))
		{
			$this->cacheFileRoot = str_replace('\\', '/', $hostConf["host"]);
			$this->cacheFileRoot = rtrim($this->cacheFileRoot, '\\/') . '/';
			if (isset($hostConf["key_prefix"]))
			{
				$this->cacheFileRoot .= $hostConf["key_prefix"] . '/';
			}
			return true;
		}
		else
		{
			trigger_error("Must set [host]");
			return false;
		}
	}
	/**
	 * 当key存在时:
	 * 如果没有过期, 不更新值, 返回 false
	 * 如果已经过期,   更新值, 返回 true
	 * 
	 * @return bool 
	 */
	public function add($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		$cacheFile = $this->getCacheFile($tableName, $key);
		$cachePath = pathinfo($cacheFile, PATHINFO_DIRNAME);
		if (!is_dir($cachePath))
		{
			if (!@mkdir($cachePath, 0777, true))
			{
				trigger_error("Can not create $cachePath");
			}
		}
		if (is_file($cacheFile))
		{
			$existsTtl = file_get_contents($cacheFile, false, null, 13, 10);
			if (0 == $existsTtl || time() < $existsTtl)
			{
				return false;
			}
		}
		$expireTime = (0 == $ttl) ? '0000000000' : (time() + $ttl);
		$length = file_put_contents($cacheFile, '<?php exit;?>' . $expireTime . serialize($value));
		return $length > 0 ? true : false;
	}
	/**
	 * 删除不存在的key返回false
	 * 
	 * @return bool 
	 */
	public function del($key, $tableName, $connectionResource)
	{
		$cacheFile = $this->getCacheFile($tableName, $key);
		if (!is_file($cacheFile))
		{
			return false;
		}
		else
		{
			return @unlink($cacheFile);
		}
	}
	/**
	 * 取不存在的key返回false
	 * 已经过期返回false
	 * 
	 * @return 成功返回数据,失败返回false
	 */
	public function get($key, $tableName, $connectionResource)
	{
		$cacheFile = $this->getCacheFile($tableName, $key);
		if (!is_file($cacheFile))
		{
			return false;
		}
		else
		{ 
			// php > 5.1.0
			$ttl = file_get_contents($cacheFile, false, null, 13, 10);
			if (0 != $ttl && time() > $ttl)
			{
				@unlink($cacheFile);
				return false;
			}
			else
			{
				return unserialize(file_get_contents($cacheFile, false, null, 23));
			}
		}
	}
	/**
	 * 更新不存在的key返回false
	 * 不管有没有过期,都更新数据
	 * 
	 * @return bool 
	 */
	public function update($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		$cacheFile = $this->getCacheFile($tableName, $key);
		if (!is_file($cacheFile))
		{
			return false;
		}
		$expireTime = (0 == $ttl) ? '0000000000' : (time() + $ttl);
		$length = file_put_contents($cacheFile, '<?php exit;?>' . $expireTime . serialize($value));
		return $length > 0 ? true : false;
	}

	protected function getCacheFile($tableName, $key)
	{
		$token = md5($key);
		$cacheFile = $this->cacheFileRoot . substr($token, 0, 2) . '/' . substr($token, 2, 2);
		if ($tableName)
		{
			$cacheFile .= '/' . $tableName;
		}
		return $cacheFile . '/' . 'phps-' . $token . '.php';
	}
}
