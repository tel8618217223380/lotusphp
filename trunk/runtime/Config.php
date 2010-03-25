<?php
class LtConfig
{
	public static $storeHandle;
	protected $conf;
	private $cacheVar;

	public function __construct()
	{
		if (!is_object(self::$storeHandle))
		{
			self::$storeHandle = new LtStoreMemory;
		}
	}

	public function init()
	{
		//don't removeme, I am the placeholder
	}

	public function get($key)
	{
		if(!isset($this->cacheVar[$key]))
		{
			$this->cacheVar[$key] = self::$storeHandle->get($key);
		}
		return $this->cacheVar[$key];
	}

	/**
	 * 警告
	 * 这里会包含两个用户定义的配置文件，为了不和配置文件里的变量名发生重名
	 * 本方法不定义和使用变量名
	 */
	public function loadConfigFile($configFile)
	{
		if (0 == self::$storeHandle->get(".config_total"))
		{
			if (null === $configFile || !is_file($configFile))
			{
				trigger_error("no config file specified or invalid config file");
			}
			$this->conf = include($configFile);
			if (!is_array($this->conf))
			{
				trigger_error("config file do NOT return array: $configFile");
			}
			elseif (!empty($this->conf))
			{
				if (0 == self::$storeHandle->get(".config_total"))
				{
					self::$storeHandle->add(".config_total", 0);
				}
				$this->addConfig($this->conf);
			}
		}
	}

	public function addConfig($configArray)
	{
		foreach($configArray as $key => $value)
		{
			self::$storeHandle->add($key, $value);
			self::$storeHandle->update(".config_total", self::$storeHandle->get(".config_total") + 1, 0);
		}
	}

	public function updateConfig($configArray)
	{
		foreach($configArray as $key => $value)
		{
			self::$storeHandle->update($key, $value);
		}
	}
}
