<?php
class GroupsDao
{
	public $uid;
	private $groups;

	public function __construct()
	{
		$db = LtObjectUtil::singleton('LtDb');
		$db->group = "group_0";
		$db->node = "node_0";
		$db->init(); 
		$this->groups = $db->getTDG("groups");
	}
	public function getAll()
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
			$condition['orderby'] = 'gid DESC';

			$result['count'] = $this->groups->count($condition);
			$result['rows'] = $this->groups->fetchRows($condition);
		}
		return $result;
	}
	public function getList($limit = 25, $offset = 0)
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
			$condition['limit'] = $limit;
			$condition['offset'] = $offset;
			$condition['orderby'] = 'id DESC';

			$result['count'] = $this->groups->count($condition);
			$result['rows'] = $this->groups->fetchRows($condition);
		}
		return $result;
	}
	public function get($gid)
	{
		$condition['where']['expression'] = "gid=:gid AND uid=:uid";
		$condition['where']['value']['gid'] = $gid;
		$condition['where']['value']['uid'] = $this->uid;
		$tmp = array();
		$tmp = $this->groups->fetchRows($condition);
		$result = $tmp ? $tmp[0] : $tmp; 
		return $result;
	}

	public function add($data)
	{ 
		$result = $this->groups->insert($data);
		return $result;
	}

	public function edit($data)
	{
		$gid = $data['gid'];
		unset($data['gid']);
		$condition['expression'] = "gid=:gid AND uid=:uid";
		$condition['value']['gid'] = $gid;
		$condition['value']['uid'] = $this->uid;
		$this->groups->updateRows($condition, $data);
	}

	public function delete($gid)
	{
		if (is_array($gid))
		{
			array_map(array(&$this, 'delete'), $id);
		}
		else
		{
			$condition['expression'] = "gid=:gid AND uid=:uid";
			$condition['value']['gid'] = $gid; 
			$condition['value']['uid'] = $this->uid;
			$this->groups->deleteRows($condition); 
		}
	}
}
