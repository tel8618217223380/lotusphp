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

	public function addRole($userRole)
	{
		return $this -> adapter -> addRole($userRole);
	}

	public function addResource($resource)
	{
		return $this -> adapter -> addResource($resource);
	}

	public function allow($role, $resource)
	{
		return $this -> adapter -> allow($role, $resource);
	}

	public function deny($role, $resource)
	{
		return $this -> adapter -> deny($role, $resource);
	}

	public function addUser($user, $roles)
	{
		return $this -> adapter -> addUser($username, $roles);
	}

	public function checkAcl($user, $resource)
	{
		return $this -> adapter -> checkAcl($user, $resource);
	}
}
