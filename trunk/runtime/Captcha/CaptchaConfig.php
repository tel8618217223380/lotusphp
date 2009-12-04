<?php
class LtCaptchaConfig
{
	public $secretKey;
	public $length = 6;
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

	/** Debug? */
	public $debug = false;

	/** Image format: jpeg or png */
	public $imageFormat = 'jpeg';
}