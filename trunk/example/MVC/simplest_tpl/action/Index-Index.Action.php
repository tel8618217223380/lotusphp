<?php
class IndexIndexAction extends LtAction
{
	public function execute()
	{
		$this->code = 200;
		$this->message = "Welcome, please signin";
		$this->data["username"] = "lotusphp";

		$this->responseType = 'tpl'; // ʹ��ģ������
		$this->layout = 'top_navigator';
	}
}