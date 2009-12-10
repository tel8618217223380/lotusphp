<?php
abstract class LtRbacAdapter
{
	public $options;
	
	abstract public function init();

	abstract public function addRole($role, $comment);
	abstract public function delRole($role);
	abstract public function getRole();

	abstract public function addResource($resource,$comment);
	abstract public function delResource($resource);
	abstract public function getResource();

	abstract public function allow($role, $resource);
	abstract public function deny($role, $resource);
	abstract public function delRoleAcl($role);
	abstract public function getAcl();

	abstract public function addUser($user, $roles);
	abstract public function delUser($user);
	abstract public function getUserRole($user);

	abstract public function checkAcl($user, $resource);

}
