@ECHO off

:: rebuild the phar

CALL walker phar 1>NUL

:: ship it away from the source tree to prove its working.

CP build\walker.phar C:\Local\Apps 1>NUL
