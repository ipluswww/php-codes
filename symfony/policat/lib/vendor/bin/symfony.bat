@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../lexpress/symfony1/data/bin/symfony
php "%BIN_TARGET%" %*
