<?php
/**
* The RBAC class
*/
class LtRBAC
{
	public $userRole;
	public $role;
	public $acl; // accessControl
	public $permission;

	public function __construct()
	{

	}

	public function setUserRole($userRole)
	{
		$this->userRole = str_replace(' ', '', $userRole);
	}

	public function getUserRole()
	{
		foreach($this->userRole as $userRole)
		{
			return explode(',', trim($userRole, ','));
		}

	}

	public function setRole($role)
	{
		$this->role = $role;
	}

	public function setAcl($acl)
	{
		$this->acl = $acl;
	}

	public function setPermissions($permissions)
	{
		$this->permission = $permissions;
	}

	public function checkRole($role)
	{
		if (isset($this->acl['allow'][$role]) || isset($this->acl['deny'][$role]))
		{
			return true;
		}
		return false;
	}
/**
设置完
	用户角色表
	角色表
	角色权限表
	权限资源表
判断一个权限资源的权限

@param string $resource 权限资源名字 / 用来分层 
@return bool
*/
	public function checkAcl($resource)
	{
		$allow = false;
		$userRoles = $this->getUserRole();
		foreach($userRoles as $role)
		{
			if (isset($this->acl['allow'][$role]))
			{
				// 任意角色
				if (in_array($resource,$this->acl['allow']['*']))
				{
					$allow = true;
				}
				// 用户角色
				if (in_array($resource,$this->acl['allow'][$role]))
				{
					$allow = true;
				}
				else
				{
					$res = explode('/', trim($resource, '/'));		
					for ($i = count($res)-1; $i >= 0; $i--)
					{
						$res[$i] = '*';	
						$tmp = implode('/', $res);
						if (in_array($tmp,$this->acl['allow'][$role]))
						{
							$allow = true;
						}					
					}
				}
			}
		}
		// deny 优先
		foreach($userRoles as $role)
		{
			if (isset($this->acl['deny'][$role]))
			{
				// 任意角色
				if (in_array($resource,$this->acl['deny']['*']))
				{
					$allow = false;
				}
				// 用户角色
				if(in_array($resource,$this->acl['deny'][$role]))
				{
					$allow = false;
				}
				else
				{
					$res = explode('/', trim($resource, '/'));
					for($i = count($res)-1; $i >= 0; $i--)
					{
						$res[$i] = '*';	
						$tmp = implode('/', $res);
						if(in_array($tmp,$this->acl['deny'][$role]))
						{
							$allow = false;
						}					
					}
				}
			}
		}
		return $allow;
	}


}