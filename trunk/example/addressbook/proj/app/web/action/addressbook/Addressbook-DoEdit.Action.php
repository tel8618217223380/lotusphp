<?php
class AddressbookDoEditAction extends MyAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl';
		$this->layout = 'result';
	}

	public function execute()
	{
		$data = $this->context->post('data');
		$data['uid'] = $this->data['uid'];
		$addressbook = new AddressbookDao;

		$addressbook->edit($data);

		$this->code = 200;
		$this->data['forward'] = C('LtUrl')->generate('Default', 'Index');

		$this->data['title'] = 'addressbook';
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl');
	}
}
