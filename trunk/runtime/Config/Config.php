<?php
class LtConfig
{
	static public $storeHandle;
	public $configFile;

	/**
	 * 警告
	 * 这里会包含两个用户定义的配置文件，为了不和配置文件里的变量名发生重名
	 * 本方法不定义和使用变量名
	 */
	public function init()
	{
		if (null === $this->configFile || !is_file($this->configFile))
		{
			trigger_error("no config file specified or invalid config file");
		}
		if (!is_object(self::$storeHandle))
		{
			self::$storeHandle = new LtConfigStore;
		}
		if (0 == self::$storeHandle->get(".config_total"))
		{
			$conf = include($this->configFile);
			if (!is_array($conf))
			{
				trigger_error("Not return array");
			}
			if (!empty($conf))
			{
				self::$storeHandle->add(".config_total", 0, 0);
				$this->storeConfigArray($conf);
			}
		}
	}

	public function get($key)
	{
		$ret = self::$storeHandle->get($key);
		if (false === $ret)
		{
			trigger_error("key not exists");
		}
		return $ret;
	}

	protected function storeConfigArray($configArray)
	{
		foreach($configArray as $key => $value)
		{
			self::$storeHandle->add($key, $value, 0);
			self::$storeHandle->update(".config_total", self::$storeHandle->get(".config_total") + 1, 0);
		}
	}
}

class LtConfigStore
{
	protected $stack;

	public function add($key, $value, $ttl)
	{
		$this->stack[$key] = $value;
		return true;
	}

	public function del($key)
	{
		if (isset($this->stack[$key]))
		{
			unset($this->stack[$key]);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get($key)
	{
		return isset($this->stack[$key]) ? $this->stack[$key] : false;
	}

	public function update($key, $value, $ttl)
	{
		$this->stack[$key] = $value;
		return true;
	}
}
