<?php
/**
 * The Component class
 */
abstract class LtComponent
{
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
	 * A flag to indicate if subclass call LtComponent::__construct()
	 * 
	 * @var boolean 
	 */
	public $constructed = false;
	/**
	 * The response type
	 * 
	 * @var string 
	 */
	protected $responseType = "html";

	/**
	 * Result properties
	 */
	protected $code;

	protected $message;

	public $data;

	protected $view;

	protected $layout;

	/**
	 * The constructor function
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
		$this->beforeExecute();
		$this->execute();
		$this->writeResponse();
	}

	protected function afterConstruct()
	{ 
		
	}
	/**
	 * Do something before subClass::execute().
	 */
	protected function beforeExecute()
	{ 
		
	}

	protected function execute()
	{

	}

	protected function writeResponse()
	{
		switch ($this->responseType)
		{
			case 'json':
				echo json_encode(array("code" => $this->code,
						"message" => $this->message,
						"data" => $this->data
						));
				break;
			case 'tpl':
				if (null === $this->view)
				{
					$this->view = new LtTemplateView;
				}
				$this->view->component = true;
				$this->view->context = $this->context;
				$this->view->code = $this->code;
				$this->view->message = $this->message;
				$this->view->data = $this->data;
				$this->view->layoutDir = $this->viewDir . "layout/";
				$this->view->layout = $this->layout;
				$this->view->templateDir = $this->viewDir;
				$this->view->compiledDir = $this->viewDir . "tpl_obj/";
				$this->view->template = $this->context->uri["module"] . "_" . $this->context->uri["component"];
				$this->view->render();
				break;

			case 'html':
			case 'wml':
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
				$this->view->layout = $this->layout;
				$this->view->templateDir = $this->viewDir;
				$this->view->template = $this->context->uri["module"] . "_" . $this->context->uri["component"];
				$this->view->render();
				break;
		}
	}

}
