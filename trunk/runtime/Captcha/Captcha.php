<?php
class LtCaptcha
{
	public $conf;
	public $imageEngine;

	public function __construct()
	{
		$this->conf = new LtCaptchaConfig();
		$this->conf->seedFileRoot = rtrim($this->conf->seedFileRoot, '\/') . DIRECTORY_SEPARATOR;
	}

	public function init()
	{
		//don't remove me, I am the placeholder :)
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
			$this->imageEngine = new LtCaptchaImageEngine();
			$this->imageEngine->conf = $this->conf;
		}		
		$word = $this->generateRandCaptchaWord($seed);
		$this->saveCaptchaWord($seed, $word);
		return $this->imageEngine->drawImage($word);
	}

	public function verify($seed, $userInput)
	{
		if ($word = $this->getSavedCaptchaWord($seed))
		{
			$this->delSeedFile($seed);
			return $userInput === $word;
		}
		else
		{
			return false;
		}
	}

	protected function saveCaptchaWord($seed, $word)
	{
		$seedFile = $this->getSeedFile($seed);
		$dir = dirname($seedFile);
		if (!is_dir($dir))
		{
			mkdir($dir, 0777, true);
		}
		file_put_contents($seedFile, '<?php exit;?>' . $word);
	}

	protected function getSavedCaptchaWord($seed)
	{
		$seedFile = $this->getSeedFile($seed);
		if (!file_exists($seedFile))
		{
			return false;
		}
		else
		{
			return file_get_contents($seedFile, false, null, 13);
		}
	}

	protected function delSeedFile($seed)
	{
		unlink($this->getSeedFile($seed));
	}

	protected function getSeedFile($seed)
	{
		$token = md5($seed);
		return $this->conf->seedFileRoot . substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .  DIRECTORY_SEPARATOR . "seed-$token.php";
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