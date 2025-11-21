# 🦖 MorteArkManager - ARK: Survival Evolved Web Manager

A comprehensive PHP-based web management panel for ARK: Survival Evolved dedicated servers running on Windows. Streamline your server administration with an intuitive web interface featuring real-time monitoring, configuration management, and powerful automation tools.

## ✨ Features

### 🎮 Server Management
- ✅ **Dashboard** - Real-time server status overview with quick actions
- ✅ **Server Control** - Start, stop, restart, and force kill servers
- ✅ **Multi-Server Support** - Manage multiple ARK servers from one interface
- ✅ **Scheduled Restarts** - Configure automatic weekly server restart schedules
- ✅ **Process Monitoring** - Track ARK server processes with PID, memory usage, and uptime

### ⚙️ Configuration Tools
- ✅ **INI File Editor** - Edit GameUserSettings.ini, Game.ini, and Engine.ini with syntax highlighting
- ✅ **INI Comparison Tool** - Analyze your configuration files:
  - View current settings vs. available settings
  - Identify missing settings you can add
  - Detect unknown/custom settings
  - Get detailed descriptions and valid ranges for all settings
- ✅ **Manager Settings** - Configure ARK Manager paths and backup settings
- ✅ **Automatic Backups** - All configuration changes create automatic backups

### 🎛️ Server Control
- ✅ **RCON Console** - Execute remote console commands with quick action buttons
- ✅ **Character Transfer** - Transfer players between maps/servers safely
- ✅ **File Browser** - Navigate and edit server files like FTP
- ✅ **Script Executor** - Run batch files and custom commands with real-time output

### 📊 Monitoring & Logs
- ✅ **System Monitor** - Comprehensive real-time system monitoring:
  - CPU usage and temperature
  - Memory usage (Used/Total GB)
  - GPU usage, temperature, and memory
  - Network traffic (Received/Sent KB/s)
  - Disk usage for all drives
  - System temperatures (CPU, GPU, Motherboard)
  - Fan speeds (RPM)
  - System uptime
  - ARK server process details
- ✅ **Real-time Logs** - View server and manager logs with auto-refresh
- ✅ **Action Logging** - Audit trail of all manager actions

### 🔒 Security
- ✅ **Secure Authentication** - .htaccess password protection
- ✅ **Role-Based Access** - Admin and Moderator permission levels
- ✅ **Input Validation** - Path sanitization and validation
- ✅ **Automatic Backups** - Safety net before all file modifications

## 📋 Requirements

- **Windows 11 Pro** (or Windows Server 2019+)
- **Apache 2.4+** with mod_rewrite enabled
- **PHP 8.3+** with the following:
  - `exec()` function enabled
  - `shell_exec()` enabled
  - `proc_open()` enabled
- **ARK: Survival Evolved Dedicated Server**
- **RCON enabled** on your ARK servers
- **Hardware Monitoring** (for System Monitor features)

## 🚀 Quick Installation

### Automated Installation (Recommended)

1. **Run the installer as Administrator:**
   ```batch
   Right-click install.bat → Run as administrator
   ```

2. **Follow the prompts to:**
   - Create directory structure
   - Set permissions
   - Create .htpasswd authentication
   - Verify PHP and Apache installation

3. **Complete setup:**
   - Copy all PHP files to `H:\ark-manager\`
   - Edit `config.php` with your server settings
   - Restart Apache
   - Access `http://localhost/ark-manager`

### Manual Installation

See the **Detailed Installation Guide** section below.

## 📁 Directory Structure

```
H:\ark-manager\
├── assets/
│   ├── css/
│   │   └── style.css                # Main stylesheet
│   └── js/
│       └── main.js                  # JavaScript functions
├── includes/
│   ├── header.php                   # Header template
│   ├── footer.php                   # Footer template
│   ├── functions.php                # Core functions
│   └── rcon.php                     # RCON library
├── ini-editor/
│   └── index.php                    # INI file editor
├── ini_comparison/
│   └── index.php                    # INI comparison tool
├── server-control/
│   └── index.php                    # Server control panel
├── rcon/
│   └── index.php                    # RCON console
├── character-transfer/
│   └── index.php                    # Character transfer tool
├── file-browser/
│   └── index.php                    # File browser
├── scripts/
│   └── index.php                    # Script executor
├── logs/
│   └── index.php                    # Log viewer
├── monitor/
│   └── index.php                    # System monitor
├── settings/
│   └── index.php                    # Manager settings
├── secure/
│   └── (authentication files)       # Security components
├── config.php                       # Main configuration
├── index.php                        # Dashboard
└── .htaccess                        # Apache authentication
```

## ⚙️ Detailed Installation Guide

### Step 1: Configure Apache

Edit your Apache `httpd.conf` (typically located at `H:\apache24\conf\httpd.conf`):

```apache
# Enable mod_rewrite if not already enabled
LoadModule rewrite_module modules/mod_rewrite.so

# Add ARK Manager directory configuration
<Directory "H:/ark-manager">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

# Create alias for ARK Manager
Alias /ark-manager "H:/ark-manager"
```

**Restart Apache after changes:**
```batch
net stop Apache2.4
net start Apache2.4
```

### Step 2: Create .htpasswd Authentication

Open Command Prompt as Administrator:

```batch
cd H:\apache24\bin
htpasswd -c H:\ark-manager\.htpasswd admin
```

Enter a strong password when prompted. To add more users:

```batch
htpasswd H:\ark-manager\.htpasswd username
```

### Step 3: Configure config.php

Edit `config.php` with your specific paths and server details:

```php
<?php
// ============================================
// ARK Manager Configuration
// ============================================

// ARK Server Root Directory
define('ARK_ROOT', 'C:\\ARKServers\\ARKASE');

// Batch Files Directory
define('BATCH_DIR', 'C:\\ARKServers');

// Configuration Directory
define('CONFIG_DIR', ARK_ROOT . '\\ShooterGame\\Saved\\Config\\WindowsServer');

// Saved Files Directory
define('SAVED_DIR', ARK_ROOT . '\\ShooterGame\\Saved');

// Cluster Directory (for character transfers)
define('CLUSTER_DIR', 'C:\\ARKServers');

// Backup Settings
define('BACKUP_DIR', ARK_ROOT . '\\Backups');
define('BACKUP_DATE_FORMAT', 'Y-m-d_H-i-s');
define('BACKUP_PREFIX', 'backup_');

// ============================================
// Server Configurations
// ============================================

$SERVERS = [];

$SERVERS['extinction'] = [
    'name' => 'Ark_Morte_Extinction',
    'map' => 'Extinction',
    'rcon_ip' => '127.0.0.1',
    'rcon_port' => 27030,
    'port' => 7789,
    'query_port' => 27035,
    'save_dir' => 'ExtinctionSave',
    'batch_file' => 'extinction'
];

$SERVERS['fjordur'] = [
    'name' => 'Ark_Morte_Fjordur',
    'map' => 'Fjordur',
    'rcon_ip' => '127.0.0.1',
    'rcon_port' => 27020,
    'port' => 7779,
    'query_port' => 27025,
    'save_dir' => 'FjordurSave',
    'batch_file' => 'fjordur'
];

// ============================================
// Batch File Paths
// ============================================

$BATCH_FILES = [];
$BATCH_FILES['extinction'] = BATCH_DIR . '\\start_extinction.bat';
$BATCH_FILES['fjordur'] = BATCH_DIR . '\\start_fjordur.bat';

// ============================================
// Player Steam IDs and Names
// ============================================

$PLAYERS = [
    '76561198012345678' => 'PlayerName1',
    '76561198087654321' => 'PlayerName2',
    // Add more players as needed
];

// ============================================
// User Roles
// ============================================

$USER_ROLES = [
    'admin' => 'Admin',
    'moderator' => 'Moderator',
];

?>
```

**Important Path Notes:**
- Always use **double backslashes** (`\\`) in Windows paths
- Verify all paths exist before starting
- Use absolute paths, not relative paths

### Step 4: Set Directory Permissions

The installer handles this automatically, but if needed manually:

```batch
icacls "H:\ark-manager\logs" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T
icacls "H:\ARKServers" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T
```

### Step 5: Verify PHP Configuration

Edit `php.ini` and ensure these functions are **NOT** in `disable_functions`:

```ini
; These must be enabled:
; exec, shell_exec, proc_open, popen

; Recommended settings:
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
```

**Restart Apache** after modifying `php.ini`.

### Step 6: Create Batch Files Directory

Organize your server batch files:

```
H:\ARKServers\
├── start_extinction.bat
├── start_fjordur.bat
└── (other server batch files)
```

**Example batch file:**
```batch
@echo off
cd /d "C:\ARKServers\ARKASE\ShooterGame\Binaries\Win64"
start ShooterGameServer.exe "Extinction?listen?SessionName=Ark_Morte_Extinction?Port=7789?QueryPort=27035?RCONEnabled=True?RCONPort=27030?ServerPassword=YourPassword?ServerAdminPassword=YourAdminPassword" -culture=en
```

### Step 7: Test Installation

1. Navigate to `http://localhost/ark-manager`
2. Enter your `.htpasswd` credentials
3. Verify the dashboard loads correctly
4. Check that all menu items are accessible

## 🔧 Configuration Details

### Adding a New Server

1. Edit `config.php`
2. Add to the `$SERVERS` array:

```php
$SERVERS['newmap'] = [
    'name' => 'Ark_Morte_NewMap',
    'map' => 'NewMap',
    'rcon_ip' => '127.0.0.1',
    'rcon_port' => 27040,
    'port' => 7799,
    'query_port' => 27045,
    'save_dir' => 'NewMapSave',
    'batch_file' => 'newmap'
];

$BATCH_FILES['newmap'] = BATCH_DIR . '\\start_newmap.bat';
```

3. Create the corresponding batch file
4. Restart Apache

### Adding Players for Character Transfer

1. Locate `.arkprofile` files in your save directories:
   ```
   H:\ARKServers\ARKASE\ShooterGame\Saved\ExtinctionSave\
   ```

2. The filename is the Steam ID: `76561198012345678.arkprofile`

3. Add to `config.php`:
   ```php
   $PLAYERS = [
       '76561198012345678' => 'PlayerName',
   ];
   ```

### Configuring Scheduled Restarts

Access the **System Monitor** page and configure:
- Restart days (Sunday through Saturday)
- Shutdown time (e.g., 6:30 AM)
- Startup time (e.g., 8:30 AM)
- Recurrence pattern (Weekly)

## 📖 Usage Guide

### Dashboard
View real-time status of all servers with quick action buttons for common tasks.

### INI File Editor
1. Select the INI file (GameUserSettings.ini, Game.ini, or Engine.ini)
2. Edit settings directly in the web interface
3. Click **Save** (automatic backup created)
4. Restart the server for changes to take effect

### INI Comparison Tool
1. Navigate to **INI Check** in the menu
2. Select an INI file to analyze
3. View:
   - **Current Settings** - What's in your file
   - **Available Settings** - All possible settings
   - **Missing Settings** - Settings you can add
   - **Unknown Settings** - Custom or client-only settings
4. Each setting includes descriptions and valid value ranges

### Server Control
- **Start Server** - Launches the server using the configured batch file
- **Stop Server** - Graceful shutdown (saves world first with `SaveWorld` + `DoExit`)
- **Restart Server** - Stop → Wait → Start sequence
- **Force Kill** - Terminates the process (use only if server is frozen)

### RCON Console
1. Select your server from the dropdown
2. Type commands or use quick action buttons:
   - **SaveWorld** - Save current game state
   - **ListPlayers** - Show connected players
   - **DestroyWildDinos** - Respawn all wild dinosaurs
   - **Broadcast \<message\>** - Server-wide announcement
   - **SetTimeOfDay HH:MM:SS** - Change in-game time
3. Kick players using the dynamic player list dropdown (auto-populated from online players)
4. View command output in real-time

### Character Transfer
1. **Stop both** source and target servers
2. Navigate to **Characters**
3. Select source server
4. Select target server
5. Check characters to transfer
6. Click **Transfer Selected Characters**
7. Automatic backups are created
8. Start the destination server

### File Browser
1. Navigate directories using the web interface
2. Click **Edit** on supported files (.ini, .txt, .log, .cfg, .bat)
3. Make changes in the editor
4. Click **Save** (automatic backup created)
5. Delete files (moved to `.deleted` folder as backup)

### Script Executor
1. View available batch files
2. Click **Run Script** to execute
3. Or enter a custom command
4. View real-time output in the console

### Log Viewer
1. Select log type:
   - **Server Logs** - ARK server logs
   - **Manager Logs** - ARK Manager action logs
2. Set number of lines to display
3. Use search to filter log entries
4. Auto-refreshes every 5 seconds

### System Monitor
Real-time monitoring dashboard displays:
- **CPU** - Usage percentage and temperature
- **Memory** - Used/Total GB and usage percentage
- **GPU** - Usage, temperature, and memory
- **Network** - Received/Sent KB/s
- **Disk Usage** - All drives with space and usage percentage
- **Temperatures** - CPU, GPU, Motherboard sensors
- **Fan Speeds** - All fan RPMs
- **System Uptime** - Days, hours, minutes
- **ARK Processes** - PID, memory usage, uptime per server
- **Scheduled Restarts** - Configure and view restart schedule

### Manager Settings
Configure core manager settings:
- **Directory Paths** - ARK root directory
- **Backup Settings** - Backup directory, date format, filename prefix
- **Current Configuration** - View all configured paths
- Direct link to edit `config.php` in File Browser

## 🛠️ Troubleshooting

### "RCON Connection Failed"
**Causes:**
- Server is not running
- RCON port is incorrect
- `ServerAdminPassword` not set in GameUserSettings.ini
- Firewall blocking the RCON port

**Solutions:**
1. Verify server is running
2. Check RCON port in `config.php` matches server launch parameters
3. Add `RCONEnabled=True` and `RCONPort=27020` to server launch parameters
4. Set `ServerAdminPassword` in GameUserSettings.ini
5. Add firewall exception for RCON port

### "Failed to Execute Command"
**Causes:**
- PHP `exec()` function is disabled
- Apache doesn't have proper permissions
- Path issues

**Solutions:**
1. Check `php.ini` - ensure `exec, shell_exec, proc_open` are NOT in `disable_functions`
2. Verify Apache service runs as **LocalSystem** (default)
3. Check file permissions using the installer or `icacls` command
4. Restart Apache after any PHP configuration changes

### "File Not Found" Errors
**Causes:**
- Incorrect paths in `config.php`
- Drive letter mismatch
- Missing directories

**Solutions:**
1. Verify all paths in `config.php` use double backslashes (`\\`)
2. Check drive letters are correct (`C:\` vs `H:\`)
3. Ensure all directories exist
4. Use absolute paths, not relative paths

### Server Won't Start
**Causes:**
- Batch file doesn't exist
- Incorrect ARK executable path
- Server already running
- Port conflict

**Solutions:**
1. Verify batch file exists at the path specified in `config.php`
2. Check ARK server executable path in batch file
3. Use **Force Kill** to stop any hung processes
4. Review batch file for syntax errors
5. Check Windows Event Viewer for detailed errors

### Character Transfer Failed
**Causes:**
- Servers not stopped
- Save directories don't exist
- Player files missing
- Permission issues

**Solutions:**
1. **Always stop both servers** before transferring
2. Verify save directories exist in ARK root
3. Check `.arkprofile` and `.arktribe` files exist in source directory
4. Ensure write permissions on target directory
5. Review manager logs for specific error messages

### System Monitor Not Displaying Data
**Causes:**
- Hardware monitoring software not installed
- WMI service issues
- Permission problems

**Solutions:**
1. Ensure hardware monitoring tools are installed
2. Restart WMI service: `net stop winmgmt && net start winmgmt`
3. Run Apache as administrator (testing only)
4. Check Windows Event Viewer for WMI errors

### INI Comparison Shows No Data
**Causes:**
- Incorrect config directory path
- INI files don't exist
- Permission issues

**Solutions:**
1. Verify `CONFIG_DIR` path in `config.php`
2. Ensure INI files exist in the config directory
3. Check read permissions on config directory
4. Use **Manager Settings** to verify paths

## 📊 Logs and Auditing

### Manager Action Log
Location: `H:\ark-manager\logs\manager.log`

**Logged Information:**
- User IP address
- Timestamp
- Action performed (Start Server, Edit INI, Transfer Character, etc.)
- Details and parameters
- Success/failure status

**Example Entry:**
```
[2025-01-15 14:30:22] 192.168.1.100 - START SERVER: extinction (Success)
[2025-01-15 14:35:10] 192.168.1.100 - EDIT INI: GameUserSettings.ini (Backup created)
[2025-01-15 15:00:45] 192.168.1.100 - RCON COMMAND: SaveWorld (Success)
```

### Server Logs
Location: `H:\ARKServers\ARKASE\ShooterGame\Saved\Logs\`

View in real-time through the **Log Viewer** with auto-refresh and search capabilities.

## 🔐 Security Best Practices

### Authentication
- ✅ Use strong passwords in `.htpasswd` (minimum 12 characters)
- ✅ Only give access to trusted administrators
- ✅ Use different passwords for different users
- ✅ Consider enabling two-factor authentication at the Apache level

### Network Security
- ✅ Use HTTPS in production (configure SSL certificate in Apache)
- ✅ Restrict access by IP address if possible
- ✅ Use a VPN for remote administration
- ✅ Change default Apache and PHP ports

### File Security
- ✅ Review manager logs regularly (`logs/manager.log`)
- ✅ Backup INI files before major changes
- ✅ Keep automatic backups enabled
- ✅ Limit file browser access to necessary directories only
- ✅ Regularly review and clean old backup files

### Server Security
- ✅ Use strong RCON passwords
- ✅ Don't expose RCON ports to the internet
- ✅ Keep ARK server updated
- ✅ Monitor for suspicious activity in logs
- ✅ Test configuration changes on a backup server first

### Role-Based Access Control
* **Flexible Permission System** - Create unlimited custom roles with granular permissions
* **Pre-configured Roles:**
  * **Admin** - Full access to all features including Manager Settings
  * **Moderator** - Server control, monitoring, RCON access, player management
  * **Player** - Dashboard view, character transfers, and log access
* **Customizable** - Define your own roles (e.g., "Builder", "Community Manager", "Trial Mod") with any combination of 25+ available permissions including:
  * Page access (Dashboard, RCON, INI Editor, File Browser, etc.)
  * Server control (Start, Stop, Restart, Kill processes)
  * RCON capabilities (Execute commands, dangerous commands, kick players)
  * File management (Edit, delete, read-only access)
  * Configuration editing (Full or limited INI editing)

## ⚡ Performance Tips

### Optimize Server Performance
- Close server log viewer when not actively monitoring (reduces load)
- Use **Force Kill** only when absolutely necessary
- Schedule regular world saves via RCON (`SaveWorld` every 15-30 minutes)
- Monitor RAM usage - ARK can be memory intensive
- Keep system drivers updated for best hardware monitoring

### Optimize Manager Performance
- Pause System Monitor refresh when not actively viewing
- Limit log viewer to 100-200 lines for better performance
- Clear old backup files periodically
- Keep Apache and PHP updated to the latest stable versions

### Optimize Character Transfers
- Always stop both servers completely before transferring
- Transfer during low-activity periods
- Verify transfer success before restarting servers
- Keep cluster directory on fast storage (SSD recommended)

## 📦 Backup Strategy

### Automatic Backups
The manager automatically creates backups before:
- Editing any INI file
- Transferring characters
- Deleting files via File Browser

**Backup Locations:**
- INI files: `CONFIG_DIR\.backups\`
- Character files: `CLUSTER_DIR\.backups\`
- Deleted files: `(original location)\.deleted\`

### Manual Backup Recommendations
**Daily:**
- ARK save files (`ShooterGame\Saved\[MapName]Save\`)

**Weekly:**
- Full ARK server directory
- ARK Manager configuration (`config.php`)

**Before Updates:**
- Complete ARK server directory
- All INI configuration files
- ARK Manager directory

### Backup Tools
Consider using:
- Windows Server Backup
- Robocopy scripts
- Third-party backup software (Veeam, Acronis)
- Cloud storage sync (OneDrive, Google Drive)

## 🔄 Updating the Manager

### Update Process
1. **Backup current installation:**
   ```batch
   xcopy H:\ark-manager H:\ark-manager-backup\ /E /I /H
   ```

2. **Backup your configuration:**
   ```batch
   copy H:\ark-manager\config.php H:\config.php.backup
   copy H:\ark-manager\.htpasswd H:\.htpasswd.backup
   ```

3. **Download latest version** from repository

4. **Replace files** (except `config.php` and `.htpasswd`)

5. **Restore configuration:**
   ```batch
   copy H:\config.php.backup H:\ark-manager\config.php
   copy H:\.htpasswd.backup H:\ark-manager\.htpasswd
   ```

6. **Clear browser cache** (Ctrl+F5)

7. **Test functionality** on all pages

8. **Review changelog** for any required config.php updates

### Version History
- **Version 2.0** - Added System Monitor, INI Comparison, Scheduled Restarts, Manager Settings
- **Version 1.0** - Initial release with core features

## 🆘 Support and Documentation

### Getting Help
1. Check this README thoroughly
2. Review `quick_start.txt` for common tasks
3. Check manager logs: `H:\ark-manager\logs\manager.log`
4. Check server logs via the Log Viewer
5. Review ARK server documentation

### Common Issues Reference
- **Batch files** - No spaces in critical paths
- **RCON** - Must enable in server launch parameters with `RCONEnabled=True`
- **Permissions** - Apache user needs full access to ARK directories
- **Paths** - Always use absolute paths with double backslashes
- **PHP Functions** - Ensure `exec()` and related functions are enabled

### Reporting Issues
When reporting issues, include:
- ARK Manager version
- PHP version (`php -v`)
- Apache version
- Operating system version
- Relevant error messages from logs
- Steps to reproduce the issue

## 📜 License

This is a custom tool for personal server management. Use at your own risk.

**Disclaimer:** This software is provided "as is" without warranty of any kind. The authors are not responsible for any damage or data loss that may occur from using this software.

## 🙏 Credits

**Created by:** XxNeroMortexX  
**Purpose:** For ARK server administrators who want an easy-to-use, powerful web interface for comprehensive server management.

**Special Thanks:**
- ARK: Survival Evolved community
- PHP and Apache communities
- All contributors and testers

## 📞 Quick Reference

### File Locations
- **Web Files:** `H:\ark-manager\`
- **ARK Server:** `H:\ARKServers\ARKASE\`
- **Config Files:** `H:\ARKServers\ARKASE\ShooterGame\Saved\Config\WindowsServer\`
- **Save Files:** `H:\ARKServers\ARKASE\ShooterGame\Saved\[MapName]Save\`
- **Batch Files:** `H:\ARKServers\`
- **Manager Logs:** `H:\ark-manager\logs\`
- **Server Logs:** `H:\ARKServers\ARKASE\ShooterGame\Saved\Logs\`

### Default Access
- **URL:** `http://localhost/ark-manager`
- **Authentication:** `.htpasswd` file
- **Default User:** Set during installation

### System Requirements Summary
- Windows 11 Pro / Windows Server 2019+
- Apache 2.4+
- PHP 8.3+
- ARK: Survival Evolved Dedicated Server
- 8GB+ RAM recommended
- SSD storage recommended for best performance

---

**Version:** 2.0  
**Last Updated:** January 2025  
**Compatible With:** ARK: Survival Evolved Dedicated Server (Windows)  
**Repository:** https://github.com/XxNeroMortexX/MorteArkManager

---

**Enjoy managing your ARK servers! 🦖**