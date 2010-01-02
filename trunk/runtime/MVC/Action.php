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
	 * The context object
	 *
	 * @var object
	 */
	public $viewDir;

	/**
	 * Result properties
	 */
	protected $code;

	protected $message;

	protected $data;

	protected $view;

	/**
	 * Validate the data from client
	 * @return array
	 * @todo Validator calling
	 */
	protected function validateInput()
	{
		$validateResult = array("error_total" => 0, "error_messages" => array());
		if (0 < count($this->dtds))
		{
			
		}
		return $validateResult;
	}

	/**
	 * Check if current user have privilege to do this
	 * @return boolen
	 */
	protected function checkPrivilege()
	{
		$allow = true;
		$module = $this->context->uri["module"];
		$action = $this->context->uri["action"];
		$roles = array_merge(array("*"), $this->roles);
		/**
		 * @todo RBAC calling
		 */
		return $allow;
	}

	protected function execute()
	{

	}

	protected function writeResponse()
	{
		switch ($this->responseType)
		{
			case "html":
			case "wml":
			default:
				if (null === $this->view)
				{
					$this->view = new LtView;
				}
				$this->view->context = $this->context;
				$this->view->code = $this->code;
				$this->view->message = $this->message;
				$this->view->data = $this->data;
				$this->view->layoutDir = $this->viewDir . "layout/";
				$this->view->templateDir = $this->viewDir;
				$this->view->template = $this->context->uri["module"] . "_" . $this->context->uri["action"];
				$this->view->render();
				break;
			case "json":
				echo json_encode(array(
					"code" => $this->code,
					"message" => $this->message,
					"data" => $this->data
				));
				break;
		}
	}

	/**
	 * Do something before subClass::execute().
	 */
	protected function beforeExecute()
	{
	}

	/**
	 * Do something after subClass::__construct().
	 */
	protected function afterConstruct()
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
		$this->afterConstruct();
		$validateResult = $this->validateInput();
		if (0 == $validateResult["error_total"])
		{
			if($this->checkPrivilege())
			{
				$this->beforeExecute();
				$this->execute();
			}
			else
			{
				$this->code = 403;
				$this->message = "Access denied";
			}
		}
		else
		{
			$this->code = 407;
			$this->message = "Invalid input";
			$this->data = $validateResult["error_messages"];
		}
		$this->writeResponse();
	}
}
