<?php
class stockPriceComponent extends LtComponent
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl'; // 使用模板引擎
	}
	public function execute()
	{
		$stockInfo = array(
			'IBM' => array(80.58, 69.50, 130.93),
		);
		$this->data['companyName'] = $this->context->companyName;
		$this->data['stockPrice'] = $stockInfo[$this->context->companyName];
	}
}