@echo off
title Template 02 — Preview Server
cd /d "%~dp0samples\template-02"
echo Starting Template 02 preview server...
echo Browse to http://localhost:8080/
echo.
echo Keep this window open. Close it to stop the server.
echo.
npm run preview
pause
