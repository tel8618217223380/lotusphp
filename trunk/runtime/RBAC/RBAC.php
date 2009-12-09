<?php
/**
* The RBAC class
*/
class LtRBAC {
	protected $userRole;
	protected $role;
	protected $acl; // accessControl
	protected $permission;

	public function __construct()
	{
	}

	private function __set($p,$v)
	{
		$this->$p = $v;
	}

	private function __get($p)
	{
		if(isset($this->$p))
		{
			return($this->$p);
		}
		else
		{
			return(NULL);
		}
	}

	/**
	* 设置完
	* 用户角色表
	* 角色表
	* 角色权限表
	* 权限资源表
	* 判断一个权限资源的权限
	* 
	* @param string $resource 权限资源名字 / 用来分层
	* @return bool 
	*/
	public function checkAcl($resource)
	{
		$allow = false;
		$userRoles = explode(',', trim(array_shift($this->userRole), ','));
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
