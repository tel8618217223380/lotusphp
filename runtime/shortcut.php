<?php
function C($className)
{
	return Singleton::getInstance("Lt" . $className);
}