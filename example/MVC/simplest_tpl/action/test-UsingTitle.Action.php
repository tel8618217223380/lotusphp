<?php
class testUsingTitleAction extends LtAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl'; // ʹ��ģ������
		$this->layout = 'top_navigator';
	}
	public function execute()
	{
		$this->data['title'] = "How to use title";
	}
}