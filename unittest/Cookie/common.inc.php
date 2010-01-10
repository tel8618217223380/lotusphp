<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
require_once $lotusHome . "runtime/Cookie/Cookie.php";
require_once $lotusHome . "runtime/Cookie/CookieConfig.php";

class CookieProxy extends LtCookie
{
	public function decrypt($seed)
	{
		return parent::decrypt($seed);
	}

	public function encrypt($seed)
	{
		return parent::encrypt($seed);
	}
}