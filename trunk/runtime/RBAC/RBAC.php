<?php
class LtRbac
{
	public $adapter;
	public $conf;

	public function __construct()
	{
		$this -> conf = new LtRbacConfig;
	}

	public function init()
	{
		$adapterClassName = "LtRbacAdapter" . ucfirst($this -> conf -> adapter);
		$this -> adapter = new $adapterClassName;
		$this -> adapter -> options = $this -> conf -> options;
		$this -> adapter -> init();
	}

	public function addRole($role,$comment)
	{
		return $this -> adapter -> addRole($role,$comment);
	}

	public function delRole($role)
	{
		return $this->adapter->delRole($role);
	}

	public function getRole()
	{
		return $this->adapter->getRole();
	}

	public function addResource($resource,$comment)
	{
		return $this -> adapter -> addResource($resource,$comment);
	}

	public function getResource()
	{
		return $this->adapter->getResource();
	}

	public function delResource($resource)
	{
		return $this->adapter->delResource($resource);
	}

	public function allow($role, $resource)
	{
		return $this -> adapter -> allow($role, $resource);
	}

	public function deny($role, $resource)
	{
		return $this -> adapter -> deny($role, $resource);
	}
	
	public function delRoleAcl($role)
	{
		return $this->adapter->delRoleAcl($role);
	}
	
	public function getAcl()
	{
		return $this->adapter->getAcl();
	}

	public function addUser($user, $roles)
	{
		return $this -> adapter -> addUser($user, $roles);
	}

	public function delUser($user)
	{
		return $this -> adapter -> delUser($user);
	}

	public function getUserRole($user='')
	{
		return $this->adapter->getUserRole($user);
	}

	public function checkAcl($user, $resource)
	{
		return $this -> adapter -> checkAcl($user, $resource);
	}
}
