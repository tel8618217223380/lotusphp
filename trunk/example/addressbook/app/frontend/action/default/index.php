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
		// limit, offset, where
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
		$page = $this->context->get('page');
		$page = max(intval($page), 1);
		$page_size = $this->configHandle->get('page_size');
		if (empty($page_size))
		{
			$page_size = 25;
		}
		$limit = $page_size;
		$offset = ($page-1) * $page_size;
		// userid
		$uid = $this->data['uid'];
		// ----
		$addressBookService = new AddressBookService();
		
		// 取当前用户通讯录的所有分组
		$this->data['groups'] = $addressBookService->getAllGroups($uid);

		/**
		 * 取当前用户通讯录的列表
		 * 
		 * $result['count']
		 * $result['rows']
		 */
		$this->data['data'] = $addressBookService->getAddressBookListByUserId($uid, $limit, $offset, $where);
		$count = $this->data['data']['count'];

		// 分页  :page 会自动被替换掉
		$base_url = LtObjectUtil::singleton('LtUrl')->generate('Default', 'Index', array('page' => ':page')); 
		$pagination = new LtPagination;
		$pagination->init();
		$this->data['pages'] = $pagination->pager($page, $count, $base_url);

		// 页面标题
		$this->data['title'] = 'addressbook';

		// 入口文件url路径
		$this->data['baseurl'] = $this->configHandle->get('baseurl');
	}
}
