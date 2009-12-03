<?php
class LtCookie
{
	public $conf;

	public function __construct()
	{
		$this->conf = new LtCookieConfig();
	}

	/**
	 * Decrypt the encrypted cookie
	 *
	 * @param string $encryptedText
	 * @return string
	 */
	static private function _decrypt($encryptedText)
	{
		$key = $this->conf->secretKey;
		$cryptText = base64_decode($encryptedText);
		$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
		$decryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptText, MCRYPT_MODE_ECB, $iv);
		return trim($decryptText);
	}

	/**
	 * Encrypt the cookie
	 *
	 * @param string $plainText
	 * @return string
	 */
	static private function _encrypt($plainText)
	{
		$key = $this->conf->secretKey;
		$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
		$encryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plainText, MCRYPT_MODE_ECB, $iv);
		return trim(base64_encode($encryptText));
	}

	/**
	 * Set cookie value to deleted with $name
	 *
	 * @param array $args
	 * @return boolean
	 */
	static public function delCookie($args)
	{
		$name = $args['name'];
		$domain = isset($args['domain']) ? $args['domain'] : null;
		return isset($_COOKIE[$name]) ? setcookie($name, '', time() - 86400, '/', $domain) : true;
	}

	/**
	 * Get cookie value with $name
	 *
	 * @param string $name
	 * @return mixed
	 */
	static public function getCookie($name)
	{
		return isset($_COOKIE[$name]) ? self::_decrypt($_COOKIE[$name]) : null;
	}

	/**
	 * Set cookie
	 *
	 * @param array $args
	 * @return boolean
	 */
	static public function setCookie($args)
	{
		$name = $args['name'];
		$value = self::_encrypt($args['value']);
		$expire = isset($args['expire']) ? $args['expire'] : null;
		$path = isset($args['path']) ? $args['path'] : '/';
		$domain = isset($args['domain']) ? $args['domain'] : null;
		$secure = isset($args['secure']) ? $args['secure'] : 0;
		return setcookie($name, $value, $expire, $path, $domain, $secure);
	}
}