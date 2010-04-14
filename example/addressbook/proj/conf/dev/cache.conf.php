<?php
$ccb = new LtCacheConfigBuilder;
$ccb->addSingleHost(
	array("adapter" => "phps",
		"host" => "/tmp/addressbook/Cache-phps/"
		));

$config["cache.servers"] = $ccb->getServers();
