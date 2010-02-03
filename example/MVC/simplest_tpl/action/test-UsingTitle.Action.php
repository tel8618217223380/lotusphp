<?php
class testUsingTitleAction extends LtAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl'; // 使用模板引擎
		$this->layout = 'top_navigator';
	}
	public function execute()
	{
		$this->data['title'] = "How to use title";
	}
}