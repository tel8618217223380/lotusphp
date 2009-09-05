<?php
function C($className)
{
	return ObjectUtil::singleton("Lt" . $className);
}
