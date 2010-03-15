<?php
class LtCacheAdapterFile implements LtCacheAdapter
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
		// -- 没有过期 不更新值 返回 false
		if (is_file($cacheFile))
		{
			$data = file_get_contents($cacheFile, false, null, 6);
			$data = eval($data);
			if (0 == $data['ttl'] || time() < $data['ttl'])
			{
				return false;
			}
		} 
		// -- key不存在 或者 已经过期 更新值
		$expireTime = (0 == $ttl) ? '0000000000' : (time() + $ttl);

		$data['ttl'] = (0 >= $ttl) ? 0 : (time() + intval($ttl));
		if (is_object($value) || is_resource($value))
		{
			$length = file_put_contents($cacheFile, "<?php\nreturn array('ttl'=>" . $data['ttl'] . ",'value'=>unserialize(\"" . addslashes(serialize($value)) . "\"));\n");
		}
		else
		{
			$data['value'] = $value;
			$length = file_put_contents($cacheFile, "<?php\nreturn " . var_export($data, true) . ";\n");
		}
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
			// 使用file_get_contents + eval而不是include,
			// 防止过多include文件被apc缓存
			$data = file_get_contents($cacheFile, false, null, 6);
			$data = eval($data);
			if (0 != $data['ttl'] && time() > $data['ttl'])
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
		$data['ttl'] = (0 >= $ttl) ? 0 : (time() + intval($ttl));
		if (is_object($value) || is_resource($value))
		{
			$length = file_put_contents($cacheFile, "<?php\nreturn array('ttl'=>" . $data['ttl'] . ",'value'=>unserialize(\"" . addslashes(serialize($value)) . "\"));\n");
		}
		else
		{
			$data['value'] = $value;
			$length = file_put_contents($cacheFile, "<?php\nreturn " . var_export($data, true) . ";\n");
		}
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
		return $cacheFile . '/' . 'file-' . $token . '.php';
	}
}
