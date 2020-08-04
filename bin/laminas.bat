@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../vendor/laminas/laminas-cli/bin/laminas
php "%BIN_TARGET%" %*
