@ECHO OFF

REM ��windows������,ֱ�����б�������ͻ����ȫ��LotusPHP���

REM �����û����ϵͳPath���������php.exe ,�����ڴ�����php.exe�ļ�·�� 
SET PHPDir=D:/kiss/PHP

REM ׷�ӵ�ϵͳPath����
SET Path=%PATH%;%PHPDir%

REM �ͷ��Զ����PHPDir����
SET PHPDir=

@ECHO ON
@REM ����ȫ��LotusPHP���
php.exe TestHelper.php --verbose AllTest.php
PAUSE