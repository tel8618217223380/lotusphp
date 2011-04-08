<?php
class AddressbookEditAction extends MyAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl';
		$this->layout = 'index';
	}

	public function execute()
	{
		$groups = new GroupsDao;
		$groups->uid = $this->data['uid'];
		$this->data['groups'] = $groups->getAll();

		$id = $this->context->get('id');
		$addressbook = new AddressbookDao;
		$this->data['data'] = $addressbook->get($id);

		$this->data['title'] = 'addressbook';
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl'); 
	}
}