<?php
class LtCaptcha
{
	public $conf;
	public $imageEngine;
	static public $storeHandle;

	public function __construct()
	{
		$this->conf = new LtCaptchaConfig;
	}

	public function init()
	{
		if (!is_object(self::$storeHandle))
		{
			self::$storeHandle = new LtStoreFile;
			self::$storeHandle->setFileRoot($this->conf->seedFileRoot);
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
			$this->imageEngine = new LtCaptchaImageEngine;
			$this->imageEngine->conf = $this->conf;
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
		$allowedSymbolsLength = strlen($this->conf->allowChars) - 1;
		$captchaWord = "";
		for ($i = 0; $i < $this->conf->length; $i ++)
		{
			$captchaWord .= $this->conf->allowChars[mt_rand(0, $allowedSymbolsLength)];
		}
		return $captchaWord;
	}
}