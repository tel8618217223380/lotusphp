<?php
class LtCaptchaConfig
{
	public $allowChars = "23456789abcdeghkmnpqsuvxyz";#alphabet without similar symbols (o=0, 1=l, i=j, t=f)
	public $seedFileRoot = "/tmp/Lotus/captcha/seed/";
	public $length = 4;
	public $width = 200;
	public $height = 80;

	/**
	 * letter rotation clockwise
	 */
	public $maxRotation = 4;

	/**
	 * Internal image size factor (for better image quality)
	 * 1: low, 2: medium, 3: high
	 */
	public $scale = 2;

	/**
	 * Blur effect for better image quality (but slower image processing).
	 * Better image results with scale=3
	 */
	public $blur = false;
}