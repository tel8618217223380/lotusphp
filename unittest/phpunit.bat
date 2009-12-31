@echo off
set MyDir=D:/kiss/PHP
set Path=%PATH%;%MyDir%
set MyDir= 
cls
@echo on
php.exe TestHelper.php --verbose %*