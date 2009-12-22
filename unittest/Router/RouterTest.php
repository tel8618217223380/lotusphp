<?php
class RouterTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
        //
	}
	public function tearDown()
	{
		//
	}
    public function testDefaultModuleAction()
    {
		$router = new LtRouter();
		$this->assertEquals('Module',$router->module);
		$this->assertEquals('Action',$router->action);
    }
    public function testInitModuleAction()
    {
		$router = new LtRouter();
		$router->init();
		$this->assertEquals('Module',$router->module);
		$this->assertEquals('Action',$router->action);
    }

}