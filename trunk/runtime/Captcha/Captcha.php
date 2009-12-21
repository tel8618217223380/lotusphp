<?php
class LtCaptcha
{
	public $conf;

	public function __construct()
	{
		$this->imageEngine = new LtCaptchaImageEngine();
		$this->imageEngine->conf = $this->conf = new LtCaptchaConfig();
	}

	public function generateImage($seed)
	{
		$seedFile = $this->getSeedFile($seed);
		$word = $this->getCaptchaWord($seed);
		$dir = dirname($seedFile);
		if (!is_dir($dir))
		{
			mkdir($dir, 0777, true);
		}
		file_put_contents($seedFile, '<?php exit;?>' . $word);
		$this->imageEngine->drawImage($word);
	}

	public function verify($seed, $userInput)
	{
		$seedFile = $this->getSeedFile($seed);
		if (!file_exists($seedFile))
		{
			return false;
		}
		else
		{
			$word = file_get_contents($seedFile, false, null, 13);
			unlink($seedFile);
			return $userInput == $word;
		}
	}

	protected function getSeedFile($seed)
	{
		$this->conf->seedFileRoot = preg_match("/[\\\\|\/]$/", $this->conf->seedFileRoot) ? $this->conf->seedFileRoot : $this->conf->seedFileRoot . DIRECTORY_SEPARATOR;
		$token = md5($seed);
		return $this->conf->seedFileRoot . substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .  DIRECTORY_SEPARATOR . 'LtCaptcha-seed-' . $token . '.php';
	}

	protected function getCaptchaWord($seed)
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