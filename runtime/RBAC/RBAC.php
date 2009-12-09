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
������
	�û���ɫ��
	��ɫ��
	��ɫȨ�ޱ�
	Ȩ����Դ��
�ж�һ��Ȩ����Դ��Ȩ��

@param string $resource Ȩ����Դ���� / �����ֲ� 
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
				// �����ɫ
				if (in_array($resource,$this->acl['allow']['*']))
				{
					$allow = true;
				}
				// �û���ɫ
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
		// deny ����
		foreach($userRoles as $role)
		{
			if (isset($this->acl['deny'][$role]))
			{
				// �����ɫ
				if (in_array($resource,$this->acl['deny']['*']))
				{
					$allow = false;
				}
				// �û���ɫ
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