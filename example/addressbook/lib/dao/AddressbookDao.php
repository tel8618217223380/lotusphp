<?php
class AddressbookDao
{
	public $uid;
	private $addressbook;
	private $dbh;

	public function __construct()
	{
		$db = LtObjectUtil::singleton('LtDb');
		$db->group = "group_0";
		$db->node = "node_0";
		$db->init();
		$this->dbh = $db->getDbHandle();
		$this->addressbook = $db->getTDG("addressbook");
	}
	public function getList($limit, $offset, $where = '')
	{
		if (empty($this->uid))
		{
			$result['count'] = 0;
			$result['rows'] = array();
		}
		else
		{
			$tmp = $this->dbh->query("select count(*) total 
		from addressbook a 
		left join groups g 
		on g.gid=a.gid 
		where a.uid=$this->uid $where");
			$result['count'] = $tmp[0]['total'];
			$result['rows'] = $this->dbh->query("select a.id id, 
		a.uid uid, 
		a.gid gid,
		a.firstname firstname,
		a.lastname lastname,
		a.company company, 
		a.address address, 
		a.mobile mobile, 
		a.phone phone, 
		a.created created,
		a.modified modified,
		g.groupname groupname from addressbook a 
		left join groups g 
		on a.gid=g.gid 
		where a.uid=$this->uid $where
		limit $limit offset $offset");
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
