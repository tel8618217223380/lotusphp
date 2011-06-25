<?php
class AddressBookService
{
	protected $userDAO;
	protected $groupsDAO;
	protected $addressbookDAO;

	public function __construct()
	{
		$this->userDAO = new UserDao();
		$this->groupsDAO = new GroupsDao();
		$this->addressbookDAO = new AddressbookDao();
	}

	public function getAllGroups($uid)
	{
		$this->groupsDAO->uid = $uid;
		return $this->groupsDAO->getAll();
	}
	
	public function getAddressBookListByUserId($uid, $limit, $offset,$where)
	{
		$this->addressbookDAO->uid = $uid;
		return $this->addressbookDAO->getList($limit, $offset,$where);
	}
	public function getUserById($uid)
	{
		return $this->userDAO->get($uid);
	}
	
	public function getUserIdByName($username)
	{
		return $this->userDAO->getid($username);
	}
}
