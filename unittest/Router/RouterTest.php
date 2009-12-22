<?php
class RouterTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
	} 
	public function tearDown()
	{
	} 
	/**
	 * ÃüÁîĞĞÄ£Ê½
	 */
	public function testCLI()
	{
		$_SERVER['argv'] = array('--module', 'hello', '--action', 'world',);
		$router = new LtRouter();
		$router -> init();
		$this -> assertEquals('hello', $router -> module);
		$this -> assertEquals('world', $router -> action);
	} 
	public function testCLI2()
	{
		$_SERVER['argv'] = array('-m', 'hello', '-a', 'world',);
		$router = new LtRouter();
		$router -> init();
		$this -> assertEquals('hello', $router -> module);
		$this -> assertEquals('world', $router -> action);
	} 
	/**
	 * index.php?module=hello&action=world
	 */
	public function testNormal()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_REQUEST['module'] = 'hello';
		$_REQUEST['action'] = 'world';
		$router = new LtRouter();
		$router -> init();
		$this -> assertEquals('hello', $router -> module);
		$this -> assertEquals('world', $router -> action);
	} 
	/**
	 * index.php/hello/world
	 */
	public function testPathinfo()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER["PATH_INFO"] = '/hello/world';
		$router = new LtRouter();
		$router -> init();
		$this -> assertEquals('hello', $router -> module);
		$this -> assertEquals('world', $router -> action);
	} 
	/**
	 * index.php
	 */
	public function testDefault()
	{
		$router = new LtRouter();
		$router -> init();
		$this -> assertEquals('Module', $router -> module);
		$this -> assertEquals('Action', $router -> action);
	} 

	/**
	 * @expectedException Exception
	 */
	public function testRoute5()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_REQUEST = array(
			'module' => 'hel?lo',
			'action' => 'world',
		);
		$router = new LtRouter();
		$router -> init();
	}

	/**
	 * @expectedException Exception
	 */
	public function testRoute6()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_REQUEST = array(
			'module' => 'hello',
			'action' => 'wor/ld',
		);
		$router = new LtRouter();
		$router -> init();
	}
} 
