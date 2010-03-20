<?php
class LtStoreFile implements LtStore
{
	protected $cacheFileRoot='/tmp/Lotus/LtStoreFile/';

	public function setFileRoot($path)
	{
		/**
		 * @todo detect dir is esists and writable
		 */
			$this->cacheFileRoot = str_replace('\\', '/', $path);
			$this->cacheFileRoot = rtrim($this->cacheFileRoot, '\\/') . '/';
	}

	/**
	 * 当key存在时:
	 * 如果没有过期, 不更新值, 返回 false
	 * 如果已经过期,   更新值, 返回 true
	 * 
	 * @return bool 
	 */
	public function add($key, $value, $ttl = 0)
	{
		$file = $this->getCacheFile($key);
		$cachePath = pathinfo($file, PATHINFO_DIRNAME);
		if (!is_dir($cachePath))
		{
			if (!@mkdir($cachePath, 0777, true))
			{
				trigger_error("Can not create $cachePath");
			}
		}
		if (is_file($file))
		{
			$existsTtl = file_get_contents($file, false, null, 13, 10);
			if (0 == $existsTtl || time() < $existsTtl)
			{
				return false;
			}
		}
		$expireTime = (0 == $ttl) ? '0000000000' : (time() + $ttl);
		$length = file_put_contents($file, '<?php exit;?>' . $expireTime . $value);
		return $length > 0 ? true : false;
	}

	/**
	 * 删除不存在的key返回false
	 * 
	 * @return bool 
	 */
	public function del($key)
	{
		$file = $this->getCacheFile($key);
		if (!is_file($file))
		{
			return false;
		}
		else
		{
			return @unlink($file);
		}
	}

	/**
	 * 取不存在的key返回false
	 * 已经过期返回false
	 * 
	 * @return 成功返回数据,失败返回false
	 */
	public function get($key, $doNotModifiedSince = null)
	{
		$file = $this->getCacheFile($key);
		if (!is_file($file))
		{
			return false;
		}
		else
		{
			// $ttl = file_get_contents($file, false, null, 13, 10);
			$str = file_get_contents($file);
			$ttl = substr($str, 13, 10);
			if (0 != $ttl && time() > $ttl)
			{
				@unlink($file);
				return false;
			}
			else
			{
				if ($doNotModifiedSince && filemtime($file) > $doNotModifiedSince)
				{
					return false;
				}
				else
				{
					// return file_get_contents($file, false, null, 23);
					return substr($str, 23);
				}
			}
		}
	}

	/**
	 * 更新不存在的key返回false
	 * 不管有没有过期,都更新数据
	 * 
	 * @return bool 
	 * @todo 是否考虑不存在的 key 为 add ? 使用起来更方便?
	 */
	public function update($key, $value, $ttl = 0)
	{
		$file = $this->getCacheFile($key);
		if (!is_file($file))
		{
			return false;
		}
		$expireTime = (0 == $ttl) ? '0000000000' : (time() + $ttl);
		$length = file_put_contents($file, '<?php exit;?>' . $expireTime . $value);
		return $length > 0 ? true : false;
	}

	protected function getCacheFile($key)
	{
		$token = md5($key);
		$file = $this->cacheFileRoot . substr($token, 0, 2) . '/' . substr($token, 2, 2);
		return $file . '/' . 'phps-' . $token . '.php';
	}
}