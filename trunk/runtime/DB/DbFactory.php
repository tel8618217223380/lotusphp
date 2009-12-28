<?php
class LtDbFactory
{
	static function getConnectionAdapter($extension)
	{
		if (preg_match("/^pdo_/i", $extension))
		{
			$LtDbConnectionAdapter = "LtDbConnectionAdapterPdo";
		}
		else
		{
			$LtDbConnectionAdapter = "LtDbConnectionAdapter" . ucfirst($extension);
		}
		return new $LtDbConnectionAdapter;
	}

	static function getSqlAdapter()
	{
		if (preg_match("/^pdo_/i", $extension))
		{
			$LtDbSqlAdapter = "LtDbSqlAdapter" . ucfirst(substr($extension, 4));
		}
		else
		{
			$LtDbSqlAdapter = "LtDbSqlAdapter" . ucfirst($extension);
		}
		//Mysqli use mysql syntax
		if ("mysqli" == $extension)
		{
			$LtDbSqlAdapter = "LtDbSqlAdapterMysql";
		}
		return new $LtDbSqlAdapter;
	}
}