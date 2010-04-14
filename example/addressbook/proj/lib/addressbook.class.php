<?php
class MyAddressbook
{
	public $uid;
	private $addressbook;

	public function __construct()
	{
		$db = LtObjectUtil::singleton('LtDb');
		$db->group = "group_0";
		$db->node = "node_0";
		$db->init(); 
		$this->addressbook = $db->getTDG("addressbook");
	}
	public function getList($page, $page_size)
	{
		if(empty($this->uid))
		{
			$result['count'] = 0;
			$result['rows'] = array();
		}
		else
		{
			$condition['where']['expression'] = "uid = :uid";
			$condition['where']['value']['uid'] = $this->uid;
			$condition['limit'] = $page_size;
			$condition['offset'] = ($page-1) * $page_size;
			$condition['orderby'] = 'id DESC';

			$result['count'] = $this->addressbook->count($condition);
			$result['rows'] = $this->addressbook->fetchRows($condition);
		}
		return $result;
	}
	public function get($id)
	{
		$condition['where']['expression'] = "id = :id";
		$condition['where']['value']['id'] = $id;
		$tmp = array();
		$tmp = $this->addressbook->fetchRows($condition);
		$result = $tmp ? $tmp[0] : $tmp; 
		return $result;
	}

	public function add($data)
	{ 
		$result = $this->addressbook->insert($data);
		return $result;
	}

	public function edit($data)
	{
		$id = $data['id'];
		unset($data['id']);
		$condition['expression'] = "id = :id";
		$condition['value']['id'] = $id;
		$this->addressbook->updateRows($condition, $data);
	}

	public function delete($id)
	{
		if (is_array($id))
		{
			array_map(array(&$this, 'delete'), $id);
		}
		else
		{
			$condition['expression'] = "id = :id";
			$condition['value']['id'] = $id; 
			$this->addressbook->deleteRows($condition); 
		}
	}
}
