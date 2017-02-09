@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../lox/xhprof/bin/xhprofile
php "%BIN_TARGET%" %*
