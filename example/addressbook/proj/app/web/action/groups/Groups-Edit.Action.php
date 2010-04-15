<?php
class GroupsEditAction extends MyAction
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

		$gid = $this->context->get('gid');

		$this->data['group'] = $groups->get($gid);

		$this->data['title'] = 'addressbook';
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl'); 
	}
}
