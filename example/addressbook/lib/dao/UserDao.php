<?php
class UserDao
{
	private $user;

	public function __construct()
	{
		$db = LtObjectUtil::singleton('LtDb');
		$db->group = "group_0";
		$db->node = "node_0";
		$db->init(); 
		$this->user = $db->getTDG("user");
	}

	public function get($uid)
	{
		return $this->user->fetch($uid);
	}

	public function getid($username)
	{
		$condition['where']['expression'] = "username = :username";
		$condition['where']['value']['username'] = $username;
		$condition['limit'] = 1;
		$tmp = array();
		$tmp = $this->user->fetchRows($condition);
		return $tmp ? $tmp[0]['uid'] : $tmp;
	}

	public function add($data)
	{ 
		$result = $this->user->insert($data);
		return $result;
	}

	public function exists($data, $field = 'mobile')
	{
		$condition['where']['expression'] = "$field = :$field";
		$condition['where']['value'][$field] = $data;
		$tmp = array();
		$tmp = $this->user->fetchRows($condition);

		$result = $tmp ? true : false; 
		return $result;
	}
}
