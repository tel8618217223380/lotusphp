<?php
class AddressbookAddAction extends MyAction
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

		$this->data['title'] = 'addressbook';
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl'); 
	}
}
