<?php
class AddressbookDoDeleteAction extends MyAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl';
		$this->layout = 'result';
	}
	public function execute()
	{
		$id = $this->context->get('id');
		$addressbook = new AddressbookDao;
		$addressbook->delete($id);
		$this->code = 200;
		$this->message = '删除成功';
		$this->data['title'] = 'addressbook';
		$this->data['forward'] = C('LtUrl')->generate('Default', 'Index');
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl'); 
	}
}
