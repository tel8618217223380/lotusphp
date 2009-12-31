@echo off
set MyDir=D:/kiss/PHP
set Path=%PATH%;%MyDir%
set MyDir= 
@echo on
php.exe TestHelper.php --verbose AllTest.php
pause