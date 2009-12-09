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
	* ������
	* �û���ɫ��
	* ��ɫ��
	* ��ɫȨ�ޱ�
	* Ȩ����Դ��
	* �ж�һ��Ȩ����Դ��Ȩ��
	* 
	* @param string $resource Ȩ����Դ���� / �����ֲ�
	* @return bool 
	*/
	public function checkAcl($resource)
	{
		$allow = false;
		$userRoles = explode(',', trim(array_shift($this->userRole), ','));
		// deny ����
		foreach (array("allow", "deny") as $operation) 
		{
			foreach($userRoles as $role) 
			{
				if (isset($this -> acl[$operation][$role])) 
				{
					// �����ɫ
					if (in_array($resource, $this -> acl[$operation]['*'])) 
					{
						$allow = "allow" == $operation ? true : false;
						break;
					} 
					// �û���ɫ
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
