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
		if (null === $this->configFile || !is_file($this->configFile))
		{
			trigger_error("no config file specified or invalid config file");
		}
		if (!is_object($this->storeHandle))
		{
			$this->storeHandle = new LtConfigStore;
		}
		else
		{
			$this->namespace = md5($this->configFile);
			$this->storeHandle->namespaceMapping[$this->namespace] = sprintf("%u", crc32($this->namespace));
		}
		if (0 == $this->storeHandle->get(".config_total", $this->namespace))
		{
			$this->storeHandle->add(".config_total", 0, 0, $this->namespace);
			$conf = include($this->configFile);
			if (!is_array($conf))
			{
				trigger_error("Not return array");
			}
			if (!empty($conf))
			{
				$this->storeConfigArray($conf);
			}
		}
	}

	public function get($key)
	{
		$ret = $this->storeHandle->get($key, $this->namespace);
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
			$this->storeHandle->add($key, $value, 0, $this->namespace);
			$this->storeHandle->update(".config_total", $this->storeHandle->get(".config_total", $this->namespace) + 1, 0, $this->namespace);
		}
	}
}

class LtConfigStore
{
	protected $stack;

	public function add($key, $value, $ttl, $namespace)
	{
		$this->stack[$this->getRealKey($namespace, $key)] = $value;
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