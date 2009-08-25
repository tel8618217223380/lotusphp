<?php
include "bootstrap.inc.php";

$url = new Url;
$url->conf->pattern = "standard";
$url->link(__LINE__);
$url->link(__LINE__);

Singleton::getInstance("Url")->link(__LINE__);


Singleton::getInstance("Url")->link(__LINE__);