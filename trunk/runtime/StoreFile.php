<?php
class LtStoreFile implements LtStore
{
	public $cacheFileRoot = '/tmp/Lotus/LtStoreFile/';
	public $prefix = 'LtStore-';
	public $useSerialize = false;

	public function init()
	{
		/**
		 * 目录不存在和是否可写在调用add是测试
		 * @todo detect dir is exists and writable
		 */
		$this->cacheFileRoot = str_replace('\\', '/', $this->cacheFileRoot);
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
		if ($this->useSerialize)
		{
			$value = serialize($value);
		}
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
		if ($doNotModifiedSince && filemtime($file) < $doNotModifiedSince)
		{
			return false;
		}

		$str = file_get_contents($file);
		$ttl = substr($str, 13, 10);
		if (0 != $ttl && time() > $ttl)
		{
			@unlink($file);
			return false;
		}
		else
		{
			$value = substr($str, 23);
			if ($this->useSerialize)
			{
				$value = unserialize($value);
			}
			return $value;
		}
	}

	/**
	 * key不存在 返回false
	 * 不管有没有过期,都更新数据
	 *
	 * @return bool
	 */
	public function update($key, $value, $ttl = 0)
	{
		$file = $this->getCacheFile($key);
		if (!is_file($file))
		{
			return false;
		}
		else
		{
			$expireTime = (0 == $ttl) ? '0000000000' : (time() + $ttl);
			if ($this->useSerialize)
			{
				$value = serialize($value);
			}
			$length = file_put_contents($file, '<?php exit;?>' . $expireTime . $value);
			return $length > 0 ? true : false;
		}
	}

	protected function getCacheFile($key)
	{
		$token = md5($key);
		$file = $this->cacheFileRoot . substr($token, 0, 2) . '/' . substr($token, 2, 2);
		return $file . '/' . $this->prefix . $token . '.php';
	}
}
