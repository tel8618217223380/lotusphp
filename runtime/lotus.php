<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . "Autoloader/Autoloader.php";
$autoloader = new Autoloader;
print_r($autoloader->scanDir(array(dirname(__FILE__) . DIRECTORY_SEPARATOR)));