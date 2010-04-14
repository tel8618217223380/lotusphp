<?php
class DefaultIndexAction extends MyAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl';
		$this->layout = 'index';
	}

	public function execute()
	{
		$addressbook = new MyAddressbook;
		$page = $this->context->get('page');
		$page = max(intval($page), 1); 

		$page_size = LtObjectUtil::singleton('LtConfig')->get('page_size');
		if(empty($page_size))
		{
			$page_size = 25;
		}
		$condition['limit'] = $page_size;
		$condition['offset'] = ($page-1) * $page_size;
		$condition['orderby'] = 'id DESC';

		$this->data['data'] = $addressbook->getList($condition);

		$count = $this->data['data']['count'];
		$base_url = C('LtUrl')->generate('Default', 'Index', array('page' => ':page')); // :page会自动被替换掉

		$pagination = new LtPagination;
		$pagination->init();
		$this->data['pages'] = $pagination->pager($page,$count,$base_url);

		$this->data['title'] = 'addressbook';
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl'); 
	}
}
