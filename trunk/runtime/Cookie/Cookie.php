<?php
class LtCookie
{
	public static $configHandle;

	public function __construct()
	{
		self::$configHandle = new LtConfig;
	}

	public function init()
	{ 
		// don't remove me, I am the placeholder
	}

	/**
	 * Decrypt the encrypted cookie
	 * 
	 * @param string $encryptedText 
	 * @return string 
	 */
	protected function decrypt($encryptedText)
	{
		$key = self::$configHandle->get("cookie.secret_key");
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
	protected function encrypt($plainText)
	{
		$key = self::$configHandle->get("cookie.secret_key");
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
	public function delCookie($name, $path = '/', $domain = null)
	{
		if (isset($_COOKIE[$name]))
		{
			if (is_array($_COOKIE[$name]))
			{
				foreach($_COOKIE[$name] as $k => $v)
				{
					setcookie($name . '[' . $k . ']', '', time() - 86400, $path, $domain);
				}
			}
			else
			{
				setcookie($name, '', time() - 86400, $path, $domain);
			}
		}
	}

	/**
	 * Get cookie value with $name
	 * 
	 * @param string $name 
	 * @return mixed 
	 */
	public function getCookie($name)
	{
		$ret = null;
		if (isset($_COOKIE[$name]))
		{
			if (is_array($_COOKIE[$name]))
			{
				$ret = array();
				foreach($_COOKIE[$name] as $k => $v)
				{
					$v = $this->decrypt($v);
					$ret[$k] = $v;
				}
			}
			else
			{
				$ret = '';
				$ret = $this->decrypt($_COOKIE[$name]);
			}
		}
		return $ret;
	}

	/**
	 * Set cookie
	 * 
	 * @param array $args 
	 * @return boolean 
	 */
	public function setCookie($name, $value = '', $expire = null, $path = '/', $domain = null, $secure = 0)
	{
		if (is_array($value))
		{
			foreach($value as $k => $v)
			{
				$v = $this->encrypt($v);
				setcookie($name . '[' . $k . ']', $v, $expire, $path, $domain, $secure);
			}
		}
		else
		{
			$value = $this->encrypt($value);
			setcookie($name, $value, $expire, $path, $domain, $secure);
		}
	}
}
