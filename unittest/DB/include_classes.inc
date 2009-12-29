<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
require_once $lotusHome . "runtime/DB/Db.php";
require_once $lotusHome . "runtime/DB/DbHandle.php";
require_once $lotusHome . "runtime/DB/DbConfig.php";
require_once $lotusHome . "runtime/DB/DbConfigBuilder.php";
require_once $lotusHome . "runtime/DB/DbConnectionManager.php";
require_once $lotusHome . "runtime/DB/DbFactory.php";
require_once $lotusHome . "runtime/DB/DbStaticData.php";
require_once $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapter.php";
require_once $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterMysql.php";
require_once $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterMysqli.php";
require_once $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterPdo.php";
require_once $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterPgsql.php";
require_once $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterSqlite.php";
require_once $lotusHome . "runtime/DB/adapter/SqlAdapter/DbSqlAdapter.php";
require_once $lotusHome . "runtime/DB/adapter/SqlAdapter/DbSqlAdapterMysql.php";
require_once $lotusHome . "runtime/DB/adapter/SqlAdapter/DbSqlAdapterPgsql.php";
require_once $lotusHome . "runtime/DB/adapter/SqlAdapter/DbSqlAdapterSqlite.php";