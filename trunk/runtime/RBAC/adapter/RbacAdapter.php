<?php
abstract class LtRbacAdapter
{
	public $options;
	
	abstract public function init();

	abstract public function addRole($userRole);

	abstract public function addResource($resource);

	abstract public function allow($role, $resource);

	abstract public function deny($role, $resource);

	abstract public function addUser($user, $roles);

	abstract public function checkAcl($user, $resource);

	abstract public function getUserRole($user);
}
