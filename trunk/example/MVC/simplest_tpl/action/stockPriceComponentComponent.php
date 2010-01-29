<?php
class stockPriceComponent extends LtComponent
{
	public function execute()
	{
		$stockInfo = array(
			'IBM' => array(80.58, 69.50, 130.93),
		);
		$this->data['companyName'] = $this->context->companyName;
		$this->data['stockPrice'] = $stockInfo[$this->context->companyName];
	}
}