<?php
class GroupsDoDeleteAction extends MyAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl';
		$this->layout = 'result';
	}
	public function execute()
	{
		$gid = $this->context->get('gid');
		$groups = new MyGroups;
		$groups->delete($gid);
		$this->code = 200;
		$this->message = '删除成功';
		$this->data['title'] = 'addressbook';
		$this->data['forward'] = C('LtUrl')->generate('Groups', 'Index');
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl'); 
	}
}
