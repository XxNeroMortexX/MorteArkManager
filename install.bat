@echo off
title ARK Manager Installation Script
echo ============================================
echo ARK: Survival Evolved Web Manager Installer
echo ============================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo [1/7] Creating directory structure...
mkdir "C:\WebServer\ark-manager\assets\css" 2>nul
mkdir "C:\WebServer\ark-manager\assets\js" 2>nul
mkdir "C:\WebServer\ark-manager\includes" 2>nul
mkdir "C:\WebServer\ark-manager\ini-editor" 2>nul
mkdir "C:\WebServer\ark-manager\server-control" 2>nul
mkdir "C:\WebServer\ark-manager\rcon" 2>nul
mkdir "C:\WebServer\ark-manager\character-transfer" 2>nul
mkdir "C:\WebServer\ark-manager\file-browser" 2>nul
mkdir "C:\WebServer\ark-manager\scripts" 2>nul
mkdir "C:\WebServer\ark-manager\logs" 2>nul
mkdir "C:\WebServer\ark-manager\monitor" 2>nul
mkdir "C:\WebServer\ark-manager\settings" 2>nul
echo Done!

echo.
echo [2/7] Setting permissions on logs directory...
icacls "C:\WebServer\ark-manager\logs" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T >nul 2>&1
echo Done!

echo.
echo [3/7] Checking PHP configuration...
php -v >nul 2>&1
if %errorLevel% neq 0 (
    echo WARNING: PHP not found in PATH. Make sure PHP is installed.
) else (
    echo PHP is installed!
)

echo.
echo [4/7] Checking Apache...
sc query Apache2.4 >nul 2>&1
if %errorLevel% neq 0 (
    echo WARNING: Apache2.4 service not found. Install Apache first.
) else (
    echo Apache service is running!
)

echo.
echo [5/7] Creating .htpasswd file...
echo.
echo Enter a username for the admin account:
set /p USERNAME=Username: 
echo.
echo Now we'll create the password...
if exist "C:\WebServer\bin\apache24\bin\htpasswd.exe" (
    "C:\WebServer\bin\apache24\bin\htpasswd.exe" -c "C:\WebServer\ark-manager\.htpasswd" %USERNAME%
    echo .htpasswd created successfully!
) else (
    echo WARNING: htpasswd.exe not found. You'll need to create .htpasswd manually.
    echo Location: C:\WebServer\bin\apache24\bin\htpasswd.exe
)

echo.
echo [6/7] Setting ARK directory permissions...
icacls "C:\ARKServers" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T >nul 2>&1
echo Done!

echo.
echo [7/7] Installation complete!
echo.
echo ============================================
echo NEXT STEPS:
echo ============================================
echo 1. Copy all PHP files to C:\WebServer\ark-manager\
echo 2. Edit config.php with your server settings
echo 3. Move your batch files to C:\ARKServers\batch\
echo 4. Restart Apache service
echo 5. Navigate to http://localhost/ark-manager
echo.
echo Don't forget to:
echo - Add Steam IDs to $PLAYERS array in config.php
echo - Verify RCON ports match your servers
echo - Set up your batch file paths in config.php
echo ============================================
echo.
pause