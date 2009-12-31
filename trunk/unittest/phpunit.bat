@ECHO OFF
SET MyDir=D:/kiss/PHP
SET path=%PATH%;%MyDir%
@ECHO ON
php.exe TestHelper.php --verbose %*