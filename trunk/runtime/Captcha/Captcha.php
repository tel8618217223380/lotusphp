<?php
class LtCaptcha
{
	public $conf;

	public function __construct()
	{
		$this->conf = new LtCaptchaConfig();
	}

	public function verify($seed, $userInput)
	{
		return $userInput === $this->getWord($seed);
	}

	public function generateImage($seed)
	{
		$text = $this->getWord($seed);
		$ini = microtime(true);

		/** Initialization */
		$this->ImageAllocate();

		$fontcfg  = $this->fonts[array_rand($this->fonts)];
		$this->WriteText($text, $fontcfg);

		/** Transformations */
		$this->WaveImage();
		if ($this->conf->blur && function_exists('imagefilter')) {
			imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
		}
		$this->ReduceImage();


		if ($this->conf->debug) {
			imagestring($this->im, 1, 1, $this->conf->height-8,
                "$text {$fontcfg['font']} ".round((microtime(true)-$ini)*1000)."ms",
			$this->GdFgColor
			);
		}

		/** Output */
		$this->WriteImage();
		$this->Cleanup();
	}

	protected function getWord($seed)
	{
		$allowedSymbols = "23456789abcdeghkmnpqsuvxyz"; #alphabet without similar symbols (o=0, 1=l, i=j, t=f)
		$key = $this->conf->secretKey;
		$captchaWordLength = isset($this->conf->length) ? $this->conf->length : 4;
		$hashNum = crc32($key . $seed);
		$hashNum = $hashNum * pow(37, $captchaWordLength-7);
		$allowedSymbolsLength = strlen($allowedSymbols);
		$captchaWord = "";
		while (0 < $captchaWordLength)
		{
			$remainder = $hashNum % $allowedSymbolsLength;
			$captchaWord .= $allowedSymbols[abs($remainder)];
			$hashNum = ($hashNum-$remainder) / $allowedSymbolsLength;
			$captchaWordLength --;
		}
		return $captchaWord;
	}

	public $maxWordLength = 9;

	/** Background color in RGB-array */
	public $backgroundColor = array(255, 255, 255);

	/** Foreground colors in RGB-array */
	public $colors = array(
		array(27,78,181), // blue
		array(22,163,35), // green
		array(214,36,7),  // red
	);

	/** Shadow color in RGB-array or null */
	public $shadowColor = null; //array(0, 0, 0);

	/**
	 * Font configuration
	 *
	 * - font: TTF file
	 * - spacing: relative pixel space between character
	 * - minSize: min font size
	 * - maxSize: max font size
	 */
	public $fonts = array(
        'Antykwa'  => array('spacing' => -3, 'minSize' => 27, 'maxSize' => 30, 'font' => 'AntykwaBold.ttf'),
        'Candice'  => array('spacing' =>-1.5,'minSize' => 28, 'maxSize' => 31, 'font' => 'Candice.ttf'),
        'DingDong' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 30, 'font' => 'Ding-DongDaddyO.ttf'),
        'Duality'  => array('spacing' => -2, 'minSize' => 30, 'maxSize' => 38, 'font' => 'Duality.ttf'),
        'Jura'     => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 32, 'font' => 'Jura.ttf'),
        'StayPuft' => array('spacing' =>-1.5,'minSize' => 28, 'maxSize' => 32, 'font' => 'StayPuft.ttf'),
        'Times'    => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 34, 'font' => 'TimesNewRomanBold.ttf'),
        'VeraSans' => array('spacing' => -1, 'minSize' => 20, 'maxSize' => 28, 'font' => 'VeraSansBold.ttf'),
	);

	/** Wave configuracion in X and Y axes */
	public $Yperiod    = 12;
	public $Yamplitude = 14;
	public $Xperiod    = 11;
	public $Xamplitude = 5;

	/** GD image */
	public $im;

	/**
	 * Creates the image resources
	 */
	protected function ImageAllocate() {
		// Cleanup
		if (!empty($this->im)) {
			imagedestroy($this->im);
		}

		$this->im = imagecreatetruecolor($this->conf->width*$this->conf->scale, $this->conf->height*$this->conf->scale);

		// Background color
		$this->GdBgColor = imagecolorallocate($this->im,
		$this->backgroundColor[0],
		$this->backgroundColor[1],
		$this->backgroundColor[2]
		);
		imagefilledrectangle($this->im, 0, 0, $this->conf->width*$this->conf->scale, $this->conf->height*$this->conf->scale, $this->GdBgColor);

		// Foreground color
		$color           = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
		$this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

		// Shadow color
		if (!empty($this->shadowColor) && is_array($this->shadowColor) && sizeof($this->shadowColor) >= 3) {
			$this->GdShadowColor = imagecolorallocate($this->im,
			$this->shadowColor[0],
			$this->shadowColor[1],
			$this->shadowColor[2]
			);
		}
	}

	/**
	 * Text insertion
	 */
	protected function WriteText($text, $fontcfg = array())
	{
		if (empty($fontcfg)) {
			// Select the font configuration
			$fontcfg  = $this->fonts[array_rand($this->fonts)];
		}

		// Full path of font file
		$fontfile = dirname(__FILE__) . '/fonts/' . $fontcfg['font'];

		/** Increase font-size for shortest words: 9% for each glyp missing */
		$lettersMissing = $this->maxWordLength-strlen($text);
		$fontSizefactor = 1+($lettersMissing*0.09);

		// Text generation (char by char)
		$x      = 20*$this->conf->scale;
		$y      = round(($this->conf->height*27/40)*$this->conf->scale);
		$length = strlen($text);
		for ($i=0; $i<$length; $i++) {
			$degree   = rand($this->conf->maxRotation*-1, $this->conf->maxRotation);
			$fontsize = rand($fontcfg['minSize'], $fontcfg['maxSize'])*$this->conf->scale*$fontSizefactor;
			$letter   = substr($text, $i, 1);

			if ($this->shadowColor) {
				$coords = imagettftext($this->im, $fontsize, $degree,
				$x+$this->conf->scale, $y+$this->conf->scale,
				$this->GdShadowColor, $fontfile, $letter);
			}
			$coords = imagettftext($this->im, $fontsize, $degree,
			$x, $y,
			$this->GdFgColor, $fontfile, $letter);
			$x += ($coords[2]-$x) + ($fontcfg['spacing']*$this->conf->scale);
		}
	}

	/**
	 * Wave filter
	 */
	protected function WaveImage()
	{
		// X-axis wave generation
		$xp = $this->conf->scale*$this->Xperiod*rand(1,3);
		$k = rand(0, 100);
		for ($i = 0; $i < ($this->conf->width*$this->conf->scale); $i++) 
		{
			imagecopy($this->im, $this->im,
			$i-1, sin($k+$i/$xp) * ($this->conf->scale*$this->Xamplitude),
			$i, 0, 1, $this->conf->height*$this->conf->scale);
		}

		// Y-axis wave generation
		$k = rand(0, 100);
		$yp = $this->conf->scale*$this->Yperiod*rand(1,2);
		for ($i = 0; $i < ($this->conf->height*$this->conf->scale); $i++) 
		{
			imagecopy($this->im, $this->im,
			sin($k+$i/$yp) * ($this->conf->scale*$this->Yamplitude), $i-1,
			0, $i, $this->conf->width*$this->conf->scale, 1);
		}
	}

	/**
	 * Reduce the image to the final size
	 */
	protected function ReduceImage()
	{
		$imResampled = imagecreatetruecolor($this->conf->width, $this->conf->height);
		imagecopyresampled($imResampled, $this->im,
		0, 0, 0, 0,
		$this->conf->width, $this->conf->height,
		$this->conf->width*$this->conf->scale, $this->conf->height*$this->conf->scale
		);
		imagedestroy($this->im);
		$this->im = $imResampled;
	}

	/**
	 * File generation
	 */
	protected function WriteImage() 
	{
		if ($this->conf->imageFormat == 'png' && function_exists('imagepng')) {
			header("Content-type: image/png");
			imagepng($this->im);
		} else {
			header("Content-type: image/jpeg");
			imagejpeg($this->im, null, 80);
		}
	}

	/**
	 * Cleanup
	 */
	protected function Cleanup() {
		imagedestroy($this->im);
	}
}