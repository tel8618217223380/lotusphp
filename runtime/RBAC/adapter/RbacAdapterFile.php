<?php
class LtRbacAdapterFile extends LtRbacAdapter
{
	private $userRole;
	private $role;
	private $acl; // accessControl
	private $resource;

	public function init()
	{
		include_once($this -> options -> aclFile);
		$this -> userRole = $userRole;
		$this -> role = $role;
		$this -> acl = $acl;
		$this -> resource = $resource;
	}

	private function _saveAcl()
	{
		$fileName = $this->options->aclFile;
		$data = "<?php\n";
		$data .= '$userRole = ' . var_export($this->userRole, true) . ";\n";
		$data .= '$role = ' . var_export($this->role, true) . ";\n";
		$data .= '$acl = ' . var_export($this->acl, true) . ";\n";
		$data .= '$resource = ' . var_export($this->resource, true) . ";\n";
		file_put_contents($fileName,$data);
	}

	public function addRole($role, $comment)
	{
		$this->role[$role] = $comment;
		$this->_saveAcl();
	}

	public function delRole($role)
	{
		unset($this->role[$role]);
		$this->_saveAcl();
	}

	public function getRole()
	{
		return $this->role;
	}

	public function addResource($resource,$comment)
	{
		$this->resource[$resource] = $comment;
		$this->_saveAcl();
	}

	public function delResource($resource)
	{
		unset($this->resource[$resource]);
		$this->_saveAcl();
	}

	public function getResource()
	{
		return $this->resource;
	}

	public function allow($role, $resource)
	{
		$this->acl['allow'][$role] = $resource;
		$this->_saveAcl();
	}

	public function deny($role, $resource)
	{
		$this->acl['deny'][$role] = $resource;
		$this->_saveAcl();
	}

	public function delRoleAcl($role)
	{
		unset($this->acl['allow'][$role],$this->acl['deny'][$role]);
		$this->_saveAcl();
	}

	public function getAcl()
	{
		return $this->acl;
	}

	public function addUser($user, $roles)
	{
		$this->userRole[$user] = $roles;
		$this->_saveAcl();
	}

	public function delUser($user)
	{
		unset($this->userRole[$user]);
		$this->_saveAcl();
	}

	public function getUserRole($user)
	{
		if(empty($user))
		{
			return $this->userRole;
		}
		else
		{
			return $this -> userRole[$user];
		}
	}

	public function checkAcl($user, $resource)
	{
		$allow = false;
		$userRole = $this -> getUserRole($user);
		$userRoles = explode(',', $userRole);
		// deny 优先
		foreach (array("allow", "deny") as $operation)
		{
			foreach($userRoles as $role)
			{
				if (isset($this -> acl[$operation][$role]))
				{
					// 任意角色
					if (in_array($resource, $this -> acl[$operation]['*']))
					{
						$allow = "allow" == $operation ? true : false;
						break;
					}
					// 用户角色
					if (in_array($resource, $this -> acl[$operation][$role]))
					{
						$allow = "allow" == $operation ? true : false;
						break;
					}
					else
					{
						$res = explode('/', trim($resource, '/'));
						for ($i = count($res)-1; $i >= 0; $i--)
						{
							$res[$i] = '*';
							$tmp = implode('/', $res);
							if (in_array($tmp, $this -> acl[$operation][$role]))
							{
								$allow = "allow" == $operation ? true : false;
								break;
							}
							unset($res[$i]);
						}
					}
				}
			}
		}
		return $allow;
	}
}
