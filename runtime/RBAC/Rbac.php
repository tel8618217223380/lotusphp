<?php
/**
* The RBAC class
*/
class LtRbac {
	protected $acl; // accessControl

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

	public function checkAcl($roles, $resource)
	{
		$allow = false;
		// deny priority
		foreach (array("allow", "deny") as $operation) 
		{
			foreach($roles as $role) 
			{
				if (isset($this->acl[$operation][$role])) 
				{
					// everyone *
					if (in_array($resource, $this->acl[$operation]['*'])) 
					{
						$allow = "allow" == $operation ? true : false;
						break;
					} 
					if (in_array($resource, $this->acl[$operation][$role])) 
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
							if (in_array($tmp, $this->acl[$operation][$role])) 
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
