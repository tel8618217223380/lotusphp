@ECHO OFF
SET MyDir=D:/kiss/PHP
SET Path=%PATH%;%MyDir%
@ECHO ON
php.exe TestHelper.php --verbose %*