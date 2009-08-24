<?php
include "bootstrap.inc.php";

$url = new Url;
$url->conf->pattern = "standard";
$url->link(__LINE__);
$url->link(__LINE__);
$url->link(__LINE__);
$url->link(__LINE__);
$url->link(__LINE__);

Url::singleton()->link(__LINE__);


Url::singleton()->link(__LINE__);


class Singleton
{
	function getInstance($class, $option)
	{
		
	}
}