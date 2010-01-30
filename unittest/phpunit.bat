@ECHO OFF

REM 如果您没有在系统Path变量中添加php.exe ,可以在此设置php.exe文件路径 
SET PHPDir=D:/kiss/PHP

REM 追加到系统Path变量
SET Path=%PATH%;%PHPDir%

REM 释放自定义的PHPDir变量
SET PHPDir=

@ECHO ON
@REM example: php.exe TestHelper.php --verbose Autoloader\PerformanceTuning.php
php.exe TestHelper.php --verbose %*