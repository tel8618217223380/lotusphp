<?php
/**
 * The Action class
 */
abstract class LtAction
{
	/**
	 * The Access Control List
	 * @var array
	 */
	protected $acl;

	/**
	 * A flag to indicate if subclass call LtAction::__construct()
	 * @var boolean
	 */
	public $constructed = false;

	/**
	 * The context object
	 *
	 * @var object
	 */
	public $context;

	/**
	 * The dtd config
	 *
	 * @var array
	 */
	protected $dtds = array();

	/**
	 * The view response type
	 *
	 * @var string
	 */
	public $responseType;

	/**
	 * The role array
	 *
	 * @var array
	 */
	protected $roles;

	/**
	 * The view object
	 *
	 * @var object
	 */
	public $view;
	
	/**
	 * Check if current user have privilege to do this
	 * @return boolen
	 */
	protected function _checkPrivilege()
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

	/**
	 * Validate the data from client
	 * @return array
	 */
	protected function _validateInput()
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
	 * The constructor function, initialize the URI property
	 */
	public function __construct($context)
	{
    	$this->context = $context;
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			$webRoot = substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen(realpath($_SERVER['DOCUMENT_ROOT'])));
			$this->context->uri['protocol'] = isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS'] ? 'https' : 'http';
			$this->context->uri['port'] = '80' == $_SERVER['SERVER_PORT'] && 'http' == $this->context->uri['protocol'] || '443' == $_SERVER['SERVER_PORT'] && 'https' == $this->context->uri['protocol'] ? '' : ':' . $_SERVER['SERVER_PORT'];
			$this->context->uri['host'] = $_SERVER['SERVER_NAME'];
			$this->context->uri['base'] = sprintf('%s://%s%s%s/', $this->context->uri['protocol'], $this->context->uri['host'], $this->context->uri['port'], $webRoot);
			$this->context->uri['bootstrap_file'] = basename($_SERVER['SCRIPT_FILENAME']);
			if(class_exists('Url'))
			{
				Url::$prefix = $this->context->uri['base'];
				Url::$bootstrapFile = $this->context->uri['bootstrap_file'];
				if (isset(Config::$app['url']['pattern']))
				{
					Url::$urlPattern = Config::$app['url']['pattern'];
				}
			}
		}
		if (isset(Config::$app['acl']))
		{
			$this->acl = Config::$app['acl'];
		}
		if (isset(Config::$app['dtd'][$this->context->uri['module']][$this->context->uri['action']]))
		{
			$this->dtds = Config::$app['dtd'][$this->context->uri['module']][$this->context->uri['action']];
		}
		$this->constructed = true;
	}

	/**
	 * Do something before subClass::execute().
	 */
	public function beforeExecute()
	{
	}
}
