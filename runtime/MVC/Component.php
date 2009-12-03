<?php
/**
 * The Component class
 */
abstract class LtComponent
{
	/**
	 * A flag to indicate if subclass call LtComponent::__construct()
	 * @var boolean
	 */
	public $constructed = false;

	/**
	 * The view response type
	 *
	 * @var string
	 */
	public $responseType;

	/**
	 * The view object
	 * 
	 * @var object
	 */
	public $view;

	/**
	 * The constructor function
	 */
	public function __construct($context)
	{
		$this->context = $context;
		$this->constructed = true;
	}

	/**
	 * Do something before subClass::execute().
	 */
	public function beforeExecute()
	{
	}
}
