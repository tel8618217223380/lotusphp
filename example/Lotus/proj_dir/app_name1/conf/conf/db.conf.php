<?php

$host = dirname(dirname(dirname(__FILE__))) .  DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
$dbname = 'cms.db';
$config['singleHost'] = array("adapter" => "sqlite", "host" => $host, "port"=>'', "password" => "", "dbname" => $dbname);