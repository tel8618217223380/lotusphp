<?php
class LtCaptcha
{
	public static $configHandle;
	public static $storeHandle;

	public $imageEngine;

	public function __construct()
	{
		if (! self::$configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil", false))
			{
				self::$configHandle = LtObjectUtil::singleton("LtConfig");
			}
			else
			{
				self::$configHandle = new LtConfig;
			}
		}
	}

	public function init()
	{
		if (!is_object(self::$storeHandle))
		{
			self::$storeHandle = new LtStoreFile;
			$seedFileRoot = self::$configHandle->get("captcha.seed_file_root");
			self::$storeHandle->cacheFileRoot = $seedFileRoot;
			self::$storeHandle->prefix = 'LtCaptcha-seed-';
			self::$storeHandle->init();
		}
	}

	public function getImageResource($seed)
	{
		if (empty($seed))
		{
			trigger_error("empty seed");
			return false;
		}
		if (!is_object($this->imageEngine))
		{
			if ($imageEngine = self::$configHandle->get("captcha.image_engine"))
			{
				if (class_exists($imageEngine))
				{
					$this->imageEngine = new $imageEngine;
					$this->imageEngine->conf = self::$configHandle->get("captcha.image_engine_conf");
				}
				else
				{
					trigger_error("captcha.image_engine : $imageEngine not exists");
				}
			}
			else
			{
				trigger_error("empty captcha.image_engine");
				return false;
			}
		}
		$word = $this->generateRandCaptchaWord($seed);
		self::$storeHandle->add($seed, $word);
		return $this->imageEngine->drawImage($word);
	}

	public function verify($seed, $userInput)
	{
		if ($word = self::$storeHandle->get($seed))
		{
			self::$storeHandle->del($seed);
			return $userInput === $word;
		}
		else
		{
			return false;
		}
	}

	protected function generateRandCaptchaWord()
	{
		$allowChars = self::$configHandle->get("captcha.allow_chars");
		$length = self::$configHandle->get("captcha.length");
		$allowedSymbolsLength = strlen($allowChars) - 1;
		$captchaWord = "";
		for ($i = 0; $i < $length; $i ++)
		{
			$captchaWord .= $allowChars[mt_rand(0, $allowedSymbolsLength)];
		}
		return $captchaWord;
	}
}
