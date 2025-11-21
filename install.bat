@echo off
title MorteArkManager Installation Script
color 0A
echo ============================================
echo   MORTEARKMANAGER v2.0 - INSTALLER
echo   ARK: Survival Evolved Web Manager
echo ============================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo [ERROR] This script must be run as Administrator!
    echo.
    echo Right-click install.bat and select "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo [INFO] Running as Administrator - OK
echo.

REM Prompt for installation directory
echo Enter installation directory (default: H:\ark-manager):
set /p INSTALL_DIR="Installation Path: "
if "%INSTALL_DIR%"=="" set INSTALL_DIR=H:\ark-manager

echo.
echo Installation directory: %INSTALL_DIR%
echo.
choice /C YN /M "Is this correct"
if errorlevel 2 goto :eof

echo.
echo ============================================
echo STEP 1/10: Creating Directory Structure
echo ============================================
echo.

mkdir "%INSTALL_DIR%" 2>nul
mkdir "%INSTALL_DIR%\assets" 2>nul
mkdir "%INSTALL_DIR%\assets\css" 2>nul
mkdir "%INSTALL_DIR%\assets\js" 2>nul
mkdir "%INSTALL_DIR%\character-transfer" 2>nul
mkdir "%INSTALL_DIR%\file-browser" 2>nul
mkdir "%INSTALL_DIR%\includes" 2>nul
mkdir "%INSTALL_DIR%\ini_comparison" 2>nul
mkdir "%INSTALL_DIR%\ini-editor" 2>nul
mkdir "%INSTALL_DIR%\logs" 2>nul
mkdir "%INSTALL_DIR%\monitor" 2>nul
mkdir "%INSTALL_DIR%\rcon" 2>nul
mkdir "%INSTALL_DIR%\scripts" 2>nul
mkdir "%INSTALL_DIR%\secure" 2>nul
mkdir "%INSTALL_DIR%\server-control" 2>nul
mkdir "%INSTALL_DIR%\settings" 2>nul

echo [OK] Directory structure created successfully!
echo.

echo ============================================
echo STEP 2/10: Setting Directory Permissions
echo ============================================
echo.

echo Setting permissions on logs directory...
icacls "%INSTALL_DIR%\logs" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Logs directory permissions set
) else (
    echo [WARNING] Could not set logs permissions
)

echo Setting permissions on secure directory...
icacls "%INSTALL_DIR%\secure" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Secure directory permissions set
) else (
    echo [WARNING] Could not set secure permissions
)

echo.

echo ============================================
echo STEP 3/10: Checking PHP Installation
echo ============================================
echo.

php -v >nul 2>&1
if %errorLevel% neq 0 (
    color 0E
    echo [WARNING] PHP not found in PATH
    echo.
    echo PHP 8.3+ is required for MorteArkManager
    echo Please install PHP and add it to your system PATH
    echo Download: https://windows.php.net/download/
    echo.
) else (
    echo [OK] PHP is installed
    php -v
    echo.
    echo Checking critical PHP functions...
    php -r "echo (function_exists('exec') ? '[OK] exec() available' : '[ERROR] exec() disabled'); echo PHP_EOL;"
    php -r "echo (function_exists('shell_exec') ? '[OK] shell_exec() available' : '[ERROR] shell_exec() disabled'); echo PHP_EOL;"
    php -r "echo (function_exists('proc_open') ? '[OK] proc_open() available' : '[ERROR] proc_open() disabled'); echo PHP_EOL;"
)

echo.

echo ============================================
echo STEP 4/10: Checking Apache Service
echo ============================================
echo.

sc query Apache2.4 >nul 2>&1
if %errorLevel% neq 0 (
    color 0E
    echo [WARNING] Apache2.4 service not found
    echo.
    echo Apache 2.4+ is required for MorteArkManager
    echo Please install Apache and ensure it's running
    echo Download: https://www.apachelounge.com/download/
    echo.
) else (
    echo [OK] Apache2.4 service found
    sc query Apache2.4 | findstr "STATE"
)

echo.

echo ============================================
echo STEP 5/10: Creating .htpasswd File
echo ============================================
echo.

echo Enter username for admin account:
set /p USERNAME=Username: 

if "%USERNAME%"=="" (
    echo [ERROR] Username cannot be empty!
    pause
    exit /b 1
)

echo.
echo Creating password for user: %USERNAME%
echo.

if exist "H:\apache24\bin\htpasswd.exe" (
    "H:\apache24\bin\htpasswd.exe" -c "%INSTALL_DIR%\.htpasswd" %USERNAME%
    if %errorLevel% equ 0 (
        echo.
        echo [OK] .htpasswd file created successfully!
    ) else (
        echo.
        echo [ERROR] Failed to create .htpasswd file
    )
) else if exist "C:\apache24\bin\htpasswd.exe" (
    "C:\apache24\bin\htpasswd.exe" -c "%INSTALL_DIR%\.htpasswd" %USERNAME%
    if %errorLevel% equ 0 (
        echo.
        echo [OK] .htpasswd file created successfully!
    ) else (
        echo.
        echo [ERROR] Failed to create .htpasswd file
    )
) else (
    color 0E
    echo.
    echo [WARNING] htpasswd.exe not found in common locations
    echo.
    echo You'll need to create .htpasswd manually:
    echo Location: %INSTALL_DIR%\.htpasswd
    echo.
    echo Try: C:\apache24\bin\htpasswd.exe -c "%INSTALL_DIR%\.htpasswd" %USERNAME%
    echo Or:  H:\apache24\bin\htpasswd.exe -c "%INSTALL_DIR%\.htpasswd" %USERNAME%
)

echo.

echo ============================================
echo STEP 6/10: ARK Server Directory Setup
echo ============================================
echo.

echo Enter your ARK server root directory (e.g., C:\ARKServers):
set /p ARK_DIR="ARK Root Directory: "

if not "%ARK_DIR%"=="" (
    if exist "%ARK_DIR%" (
        echo [OK] ARK directory exists: %ARK_DIR%
        echo.
        echo Setting permissions on ARK directory...
        icacls "%ARK_DIR%" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T >nul 2>&1
        if %errorLevel% equ 0 (
            echo [OK] ARK directory permissions set
        ) else (
            echo [WARNING] Could not set ARK permissions - you may need to do this manually
        )
    ) else (
        echo [INFO] Directory does not exist yet: %ARK_DIR%
        echo You'll need to create it and set permissions manually
    )
) else (
    echo [SKIPPED] You can configure ARK directory later in config.php
)

echo.

echo ============================================
echo STEP 7/10: Batch Files Directory
echo ============================================
echo.

echo Enter your batch files directory (e.g., C:\ARKServers):
set /p BATCH_DIR="Batch Files Directory: "

if not "%BATCH_DIR%"=="" (
    if exist "%BATCH_DIR%" (
        echo [OK] Batch directory exists: %BATCH_DIR%
    ) else (
        echo [INFO] Creating batch directory: %BATCH_DIR%
        mkdir "%BATCH_DIR%" 2>nul
        if exist "%BATCH_DIR%" (
            echo [OK] Batch directory created
        ) else (
            echo [WARNING] Could not create batch directory
        )
    )
) else (
    echo [SKIPPED] You can configure batch directory later in config.php
)

echo.

echo ============================================
echo STEP 8/10: Creating Sample .htaccess
echo ============================================
echo.

if not exist "%INSTALL_DIR%\.htaccess" (
    (
        echo AuthType Basic
        echo AuthName "ARK Manager - Restricted Access"
        echo AuthUserFile "%INSTALL_DIR%\.htpasswd"
        echo Require valid-user
        echo.
        echo ^<IfModule mod_rewrite.c^>
        echo     RewriteEngine On
        echo     RewriteBase /ark-manager/
        echo ^</IfModule^>
    ) > "%INSTALL_DIR%\.htaccess"
    
    echo [OK] .htaccess file created
) else (
    echo [INFO] .htaccess already exists - skipping
)

echo.

echo ============================================
echo STEP 9/10: PHP Configuration Check
echo ============================================
echo.

php -r "$ini = php_ini_loaded_file(); echo ($ini ? 'php.ini location: ' . $ini : 'WARNING: php.ini not found'); echo PHP_EOL;" 2>nul

echo.
echo Recommended php.ini settings:
echo   max_execution_time = 300
echo   memory_limit = 256M
echo   upload_max_filesize = 50M
echo   post_max_size = 50M
echo.
echo Critical: Ensure these are NOT in disable_functions:
echo   exec, shell_exec, proc_open, popen
echo.

echo ============================================
echo STEP 10/10: Verifying Installation
echo ============================================
echo.

echo Checking created directories...
set ERROR_COUNT=0

if exist "%INSTALL_DIR%\assets" (
    echo [OK] assets/
) else (
    echo [ERROR] assets/ missing
    set /a ERROR_COUNT+=1
)

if exist "%INSTALL_DIR%\includes" (
    echo [OK] includes/
) else (
    echo [ERROR] includes/ missing
    set /a ERROR_COUNT+=1
)

if exist "%INSTALL_DIR%\logs" (
    echo [OK] logs/
) else (
    echo [ERROR] logs/ missing
    set /a ERROR_COUNT+=1
)

if exist "%INSTALL_DIR%\monitor" (
    echo [OK] monitor/
) else (
    echo [ERROR] monitor/ missing
    set /a ERROR_COUNT+=1
)

if exist "%INSTALL_DIR%\ini_comparison" (
    echo [OK] ini_comparison/
) else (
    echo [ERROR] ini_comparison/ missing
    set /a ERROR_COUNT+=1
)

if exist "%INSTALL_DIR%\settings" (
    echo [OK] settings/
) else (
    echo [ERROR] settings/ missing
    set /a ERROR_COUNT+=1
)

echo.

if %ERROR_COUNT% gtr 0 (
    color 0E
    echo [WARNING] %ERROR_COUNT% directories missing - installation may be incomplete
) else (
    color 0A
    echo [OK] All required directories created successfully!
)

echo.
echo ============================================
echo INSTALLATION COMPLETE!
echo ============================================
echo.

color 0B

echo NEXT STEPS:
echo ============================================
echo.
echo 1. Copy all MorteArkManager PHP files to:
echo    %INSTALL_DIR%
echo.
echo 2. Edit config.php and configure:
echo    - ARK_ROOT (ARK server directory)
echo    - BATCH_DIR (batch files location)
echo    - Server configurations ($SERVERS array)
echo    - Player Steam IDs ($PLAYERS array)
echo    - Batch file mappings ($BATCH_FILES array)
echo.
echo 3. Configure Apache httpd.conf:
echo    Add this configuration block:
echo.
echo    ^<Directory "%INSTALL_DIR%"^>
echo        Options -Indexes +FollowSymLinks
echo        AllowOverride All
echo        Require all granted
echo    ^</Directory^>
echo.
echo    Alias /ark-manager "%INSTALL_DIR%"
echo.
echo 4. Restart Apache service:
echo    net stop Apache2.4
echo    net start Apache2.4
echo.
echo 5. Access the manager:
echo    http://localhost/ark-manager
echo.
echo 6. Login with your credentials:
echo    Username: %USERNAME%
echo    Password: (the one you just created)
echo.
echo ============================================
echo IMPORTANT REMINDERS:
echo ============================================
echo.
echo [!] Verify PHP has exec() enabled in php.ini
echo [!] Ensure Apache runs as LocalSystem
echo [!] Use double backslashes in config.php paths
echo [!] Enable RCON on your ARK servers
echo [!] Set ServerAdminPassword in GameUserSettings.ini
echo [!] Review README.md for detailed documentation
echo [!] Check quick_start.txt for common tasks
echo.
echo ============================================
echo INSTALLATION LOG SAVED TO:
echo %INSTALL_DIR%\installation.log
echo ============================================
echo.

REM Create installation log
(
    echo MorteArkManager Installation Log
    echo ================================
    echo Date: %date% %time%
    echo Installation Directory: %INSTALL_DIR%
    echo ARK Directory: %ARK_DIR%
    echo Batch Directory: %BATCH_DIR%
    echo Admin Username: %USERNAME%
    echo ================================
) > "%INSTALL_DIR%\installation.log"

color 0F
echo Press any key to open the README.md file...
pause >nul

if exist "%INSTALL_DIR%\README.md" (
    start "" "%INSTALL_DIR%\README.md"
) else (
    echo README.md not found in installation directory
)

echo.
echo Installation complete! Good luck with your ARK servers!
echo.
pause