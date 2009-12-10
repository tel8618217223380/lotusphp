<?php
class LtRbacAdapterFile extends LtRbacAdapter
{
	private $userRole;
	private $role;
	private $acl; // accessControl
	private $resource;

	public function init()
	{
		include_once($this -> options -> aclfile);
		$this -> userRole = $userRole;
		$this -> role = $role;
		$this -> acl = $acl;
		$this -> resource = $resource;
	}

	public function addRole($userRole)
	{
	}

	public function addResource($resource)
	{
	}

	public function allow($role, $resource)
	{
	}

	public function deny($role, $resource)
	{
	}

	public function addUser($user, $roles)
	{
	}

	public function getUserRole($user)
	{
		return $this -> userRole[$user];
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
