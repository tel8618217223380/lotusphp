<?php
class LtConfig
{
	public $storeHandle;
	public $namespace;
	public $configFile;

	/**
	 * @警告
	 * 这里会包含两个用户定义的配置文件，为了不和配置文件里的变量名发生重名
	 * 本方法不定义和使用变量名
	 */
	public function init()
	{
		if (null === $this->configFile)
		{
			trigger_error("no config file specified");
		}
		if (!is_object($this->storeHandle))
		{
			$this->storeHandle = new LtConfigStore();
		}
		if (0 == $this->storeHandle->get(".config_total", $this->namespace))
		{
			$this->configFromUserFile = include($this->configFile);
			$this->storeConfigArray(include($this->configFile));
		}
	}

	public function get($key)
	{
		return $this->storeHandle->get($key, $this->namespace);
	}

	protected function storeConfigArray($configArray)
	{
		foreach($configArray as $key => $value)
		{
			$this->storeHandle->add($key, $value, 0, $this->namespace);
		}
	}
}

class LtConfigStore
{
	protected $stack;

	public function add($key, $value, $ttl, $namespace)
	{
		$this->stack[$key = $this->getRealKey($namespace, $key)] = $value;
		return true;
	}

	public function del($key, $namespace)
	{
		$key = $this->getRealKey($namespace, $key);
		if(isset($this->stack[$key]))
		{
			unset($this->stack[$key]);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get($key, $namespace)
	{
		$key = $this->getRealKey($namespace, $key);
		return isset($this->stack[$key]) ? $this->stack[$key] : false;
	}

	public function update($key, $value, $ttl, $namespace)
	{
		$this->stack[$this->getRealKey($namespace, $key)] = $value;
		return true;
	}

	protected function getRealKey($namespace, $key)
	{
		return sprintf("%u", crc32($namespace)) . $key;
	}
}