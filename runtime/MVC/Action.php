<?php
/**
 * The Action class
 */
abstract class LtAction
{
	/**
	 * The dtd config for validator
	 *
	 * @var array
	 */
	protected $dtds = array();

	/**
	 * The Access Control List
	 * @var array
	 */
	protected $acl;

	/**
	 * The current user's roles
	 *
	 * @var array
	 */
	protected $roles = array();

	/**
	 * A flag to indicate if subclass call LtAction::__construct()
	 * @var boolean
	 */
	protected $constructed = false;

	/**
	 * The response type
	 *
	 * @var string
	 */
	protected $responseType = "html";

	/**
	 * The context object
	 *
	 * @var object
	 */
	public $context;

	/**
	 * Validate the data from client
	 * @return array
	 */
	protected function validateInput()
	{
		if (0 < count($this->dtds))
		{
			foreach ($this->dtds as $variable => $dtd)
			{
				if (!isset($dtd['from']))
				{
					$dtd['from'] = 'request';
				}
				foreach ($dtd["rules"] as $ruleKey => $ruleValue)
				{
					if ($ruleValue instanceof ConfigExpression)
					{
						eval('$_ruleValue = ' . $ruleValue->__toString());
						$dtd["rules"][$ruleKey] = $_ruleValue;
					}
				}
				$validateCondition[$variable] = array('value' => $this->context->$dtd['from']($variable), 'dtd' => $dtd);
			}
			return $validateResult = Validator::Validate($validateCondition);
		}
	}

	/**
	 * Check if current user have privilege to do this
	 * @return boolen
	 */
	protected function checkPrivilege()
	{
		$allow = false;
		$module = $this->context->uri["module"];
		$action = $this->context->uri["action"];
		foreach (array_merge(array("*"), $this->roles) as $role)
		{
			foreach (array("allow", "deny") as $operation)
			{
				if (("allow" == $operation && false == $allow || "deny" == $operation && true == $allow) && isset($this->acl[$operation][$role]))
				{
					foreach (array("$module/$action", "$module/*", "*/*") as $method)
					{
						if (in_array($method, $this->acl[$operation][$role]))
						{
							$allow = "allow" == $operation ? true : false;
							break;
						}
					}
				}
			}
		}
		return $allow;
	}

	protected function execute()
	{

	}

	protected function writeResponse()
	{

	}

	/**
	 * Do something before subClass::execute().
	 */
	protected function beforeExecute()
	{
	}

	/**
	 * The constructor function, initialize the URI property
	 */
	public function __construct()
	{
		$this->constructed = true;
	}

	public function executeChain()
	{
		if (!$this->constructed)
		{
			DebugHelper::debug('SUBCLASS_NOT_CALL_PARENT_CONSTRUCTOR', array('class' => $actionClassName));
		}
		$this->validateInput();
		$this->checkPrivilege();
		$this->beforeExecute();
		$this->execute();
		$this->writeResponse();
	}
}
