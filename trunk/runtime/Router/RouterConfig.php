<?php
// Options +FollowSymLinks
// IndexIgnore */*
// ##  Turn on the RewriteEngine
// RewriteEngine On
// ##  Rules
// RewriteCond %{REQUEST_FILENAME} !-f
// RewriteCond %{REQUEST_FILENAME} !-d
// RewriteRule . index.php
class LtRouterConfig
{
	public $module = 'Module';
	public $action = 'Action';

//	public function __construct()
//	{
//		$routingTable['pattern'] = "{module}/{action}/{*}";
//		$routingTable['config'] = array(
//			'module' => '([a-z][a-z0-9]*)*',
//			'action' => '[a-z][a-z0-9]*)*',
//			);
//		$routingTable['defaltVar'] = array(
//			'module' => 'default',
//			'action' => 'index',
//			);
//	}
}
