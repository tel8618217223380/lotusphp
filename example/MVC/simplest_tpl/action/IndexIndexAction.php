<?php
class IndexIndexAction extends LtAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl'; // ʹ��ģ������
		$this->layout = 'top_navigator';
	}

	public function execute()
	{
		$this->code = 200;
		$this->message = "Welcome, please signin";
		$this->data["username"] = "lotusphp";
	}
}