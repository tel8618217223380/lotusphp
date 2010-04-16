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
		$where = '';
		$op = $this->context->get('op');
		if ($op == 'search')
		{
			$gid = $this->context->post('gid');
			if (-1 != $gid)
			{
				$where .= " AND a.gid=$gid";
			}
			$q = $this->context->post('q');
			if ($q)
			{
				$field = $this->context->post('field');
				switch ($field)
				{
					case 'name':
						$where .= " AND a.firstname like '%$q%'";
						break;
					case 'mobile':
						$where .= " AND a.mobile='$q'";
						break;
				}
			}
		}

		$groups = new GroupsDao;
		$groups->uid = $this->data['uid'];
		$this->data['groups'] = $groups->getAll();

		$addressbook = new AddressbookDao;
		$addressbook->uid = $this->data['uid'];

		$page = $this->context->get('page');
		$page = max(intval($page), 1);

		$page_size = LtObjectUtil::singleton('LtConfig')->get('page_size');
		if (empty($page_size))
		{
			$page_size = 25;
		}
		$limit = $page_size;
		$offset = ($page-1) * $page_size;
		$this->data['data'] = $addressbook->getList($limit, $offset, $where);

		$count = $this->data['data']['count'];
		$base_url = C('LtUrl')->generate('Default', 'Index', array('page' => ':page')); // :page会自动被替换掉
		
		$pagination = new LtPagination;
		$pagination->init();
		$this->data['pages'] = $pagination->pager($page, $count, $base_url);

		$this->data['title'] = 'addressbook';
		$this->data['baseurl'] = LtObjectUtil::singleton('LtConfig')->get('baseurl');
	}
}
