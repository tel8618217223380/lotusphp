<?php
/**
* The RBAC class
*/
class LtRBAC
{
	public $userRole;
	public $role;
	public $acl; // accessControl

	private $module;
	private $action;

	public function __construct($userRole)
	{
		$this->userRole = $userRole;
		$rbac = new LtRBACConfig();
		$this->setRole($rbac->role);
		$this->setAcl($rbac->acl);
	}

	public function setRole($role)
	{
		$this->role = $role;
	}

	public function setAcl($acl)
	{
		$this->acl = $acl;
	}

	public function checkRole()
	{
		if(isset($this->role[$this->userRole]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function checkAcl()
	{
		if (isset($this->acl[$this->userRole]))
		{
			if (in_array('*', $this->acl[$this->userRole]))
			{
				return true;
			}
			if (in_array('*/*', $this->acl[$this->userRole]))
			{
				return true;
			}
			if (in_array($this->module . '/' . $this->action, $this->acl[$this->userRole]))
			{
				return true;
			}
			if (in_array($this->module . '/*', $this->acl[$this->userRole]))
			{
				return true;
			}
			if (in_array('*/' . $this->action, $this->acl[$this->userRole]))
			{
				return true;
			}
		}
		return false;
	}

	public function checkPrivilege($module,$action)
	{
		$this->module = $module;
		$this->action = $action;
		
		if(!$this->checkRole($this->userRole))
		{
			echo 'NO role';
			return false;
		}
		if(!$this->checkAcl($this->userRole))
		{
			echo "deny module=$module action=$action";
			return false;
		}
		echo "allow module=$module action=$action";
		return true;
	}
}