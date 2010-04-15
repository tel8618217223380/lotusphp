<?php
class GroupsDoEditAction extends MyAction
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

		$groups = new MyGroups;
		$groups->uid = $this->data['uid'];
		$groups->edit($data);

		$this->code = 200;
		$this->data['forward'] = C('LtUrl')->generate('Groups', 'Index');

		$this->data['title'] = 'addressbook';
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl');
	}
}
