@echo off
title Template 01 — Preview Server
cd /d "%~dp0samples\template-01"
echo Starting Template 01 preview server...
echo Browse to http://localhost:8080/
echo.
echo Keep this window open. Close it to stop the server.
echo.
npm run preview
pause
