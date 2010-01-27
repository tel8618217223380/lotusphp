<?php
class LtUrlConfig
{
	// Ĭ�ϵ�·�ɱ� 
	public $routingTable = array('pattern' => ":module/:action/*",
		'default' => array('module' => 'default', 'action' => 'index'),
		'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
		'varprefix' => ':',
		'delimiter' => '/',
		'postfix' => '',
		'protocol' => 'PATH_INFO', // standard rewrite path_info
		);
}