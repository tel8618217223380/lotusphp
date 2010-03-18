<?php
class LtConfig
{
	static public $storeHandle;
	public $configFile;
	protected $conf;

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
			self::$storeHandle = new LtStoreMemory;
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
		if (isset($this->conf[$key]))
		{
			return $this->conf[$key];
		}
		$ret = self::$storeHandle->get($key);
		if (false === $ret)
		{
			trigger_error("key not exists");
		}
		$this->conf[$key] = $ret;
		return $ret;
	}

	public function getAll()
	{
		return self::$storeHandle->get('.config_data');
	}

	protected function storeConfigArray($configArray)
	{
		foreach($configArray as $key => $value)
		{
			self::$storeHandle->add($key, $value, 0);
			self::$storeHandle->update(".config_total", self::$storeHandle->get(".config_total") + 1, 0);
		}
		self::$storeHandle->add('.config_data', $configArray, 0);
	}
}