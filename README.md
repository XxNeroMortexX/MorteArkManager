# ARK: Survival Evolved Web Manager 

A comprehensive PHP-based web management panel for ARK: Survival Evolved dedicated servers running on Windows.

## 🎮 Features

- ✅ **INI File Editor** - Edit GameUserSettings.ini, Game.ini, and Engine.ini
- ✅ **Server Control** - Start, stop, restart, and force kill servers
- ✅ **RCON Console** - Execute remote console commands
- ✅ **Character Transfer** - Transfer players between maps/servers
- ✅ **File Browser** - Browse and edit server files like FTP
- ✅ **Script Executor** - Run batch files and custom commands
- ✅ **Real-time Logs** - View server logs with auto-refresh
- ✅ **Multi-Server Support** - Manage multiple ARK servers
- ✅ **Secure Authentication** - .htaccess password protection

## 📋 requirements

- **Windows 11 Pro** (or Windows Server)
- **Apache 2.4+** with mod_rewrite
- **PHP 8.3+** with exec() enabled
- **ARK: Survival Evolved Dedicated Server**
- **RCON enabled** on your ARK servers

## 🚀 Installation

### Step 1: Create Directory Structure

```
H:\ark-manager\
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── functions.php
│   └── rcon.php
├── ini-editor/
│   └── index.php
├── server-control/
│   └── index.php
├── rcon/
│   └── index.php
├── character-transfer/
│   └── index.php
├── file-browser/
│   └── index.php
├── scripts/
│   └── index.php
├── logs/
│   └── index.php
├── config.php
├── index.php
└── .htaccess
```

### Step 2: Configure Apache

Edit your Apache `httpd.conf`:

```apache
<Directory "H/ark-manager">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    require all granted
</Directory>

Alias /ark-manager "H:/ark-manager"
```

Restart Apache after changes.

### Step 3: Create .htpasswd File

Run this command in Command Prompt:

```batch
cd H:\apache24\bin
htpasswd -c H:\ark-manager\.htpasswd admin
```

Enter your password when prompted.

### Step 4: Configure config.php

Edit `config.php` and update these values:

```php
// Update your ARK server paths
define('ARK_ROOT', 'H:\\ARKServers\\ARKASE');

// Update batch file paths
define('BATCH_DIR', 'H:\\ARKServers');

// Add your Steam IDs and player names
$PLAYERS = [
    '76561198012345678' => 'PlayerName1',
    '76561198087654321' => 'PlayerName2',
];
```

### Step 5: Create Batch Files Directory

Create `H:\ARKServers` and move your server batch files there:

```
H:\ARKServers\
├── start_extinction.bat
└── start_fjordur.bat
```

### Step 6: Set Permissions

Ensure the Apache user (LocalSystem) has read/write access to:
- `H:\ARKServers\` (entire directory)
- `H:\ark-manager\logs\` (for manager logs)

### Step 7: Verify PHP Settings

Check `php.ini` and ensure:

```ini
; These should NOT be in disable_functions
; exec, shell_exec, proc_open, popen should be enabled

max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
```

### Step 8: Test Installation

1. Navigate to `http://localhost/ark-manager`
2. Enter your .htpasswd credentials
3. You should see the dashboard

## ⚙️ Configuration

### Adding a New Server

Edit `config.php` and add to the `$SERVERS` array:

```php
$SERVERS['newmap'] = [
    'name' => 'Ark_Morte_NewMap',
    'map' => 'NewMap',
    'rcon_ip' => '192.168.1.3',
    'rcon_port' => 27040,
    'port' => 7799,
    'query_port' => 27035,
    'save_dir' => 'NewMapSave',
    'batch_file' => 'newmap'
];

$BATCH_FILES['newmap'] = BATCH_DIR . '\\start_newmap.bat';
```

### Adding Player Names

Edit `config.php` and add to `$PLAYERS`:

```php
$PLAYERS = [
    '76561198012345678' => 'John',
    '76561198087654321' => 'Jane',
    '76561198098765432' => 'Bob',
];
```

To find Steam IDs, check the `.arkprofile` filenames in your save directories.

## 🔒 Security

- ✅ .htaccess authentication protects all pages
- ✅ Input validation and path sanitization
- ✅ Automatic backups before editing files
- ✅ Action logging for audit trail
- ⚠️ Only allow trusted users access
- ⚠️ Use strong passwords in .htpasswd
- ⚠️ Consider using HTTPS in production

## 🛠️ Usage

### Starting a Server

1. Go to **Server Control**
2. Select your server
3. Click **Start Server**
4. Wait 30-60 seconds for startup

### Stopping a Server

1. Go to **Server Control**
2. Click **Stop Server** (saves world first)
3. Or use **Force Kill** if server is unresponsive

### Editing INI Files

1. Go to **INI Editor**
2. Select the INI file
3. Make your changes
4. Click **Save** (automatic backup created)
5. Restart server for changes to take effect

### Using RCON

1. Go to **RCON Console**
2. Select your server
3. Type command or use quick buttons
4. Common commands:
   - `SaveWorld` - Save current state
   - `ListPlayers` - Show online players
   - `DestroyWildDinos` - Respawn all dinos

### Transferring Characters

1. Stop both source and target servers
2. Go to **Character Transfer**
3. Select source and target servers
4. Check the characters to transfer
5. Click **Transfer Selected Characters**
6. Backups are created automatically

### Browsing Files

1. Go to **File Browser**
2. Navigate directories
3. Click **Edit** on editable files
4. Make changes and **Save**

### Running Scripts

1. Go to **Scripts & Commands**
2. Click **Run Script** on your batch file
3. Or enter a custom command
4. View output in real-time

### Viewing Logs

1. Go to **Log Viewer**
2. Select log type (Server or Manager)
3. Set number of lines to display
4. Auto-refreshes every 5 seconds
5. Use search to filter logs

## 📝 Troubleshooting

### "RCON Connection Failed"

- Check server is running
- Verify RCON port is correct
- Ensure `ServerAdminPassword` is set in GameUserSettings.ini
- Check firewall isn't blocking the port

### "Failed to Execute Command"

- Verify PHP has exec() enabled
- Check `disable_functions` in php.ini
- Ensure Apache runs as LocalSystem
- Check file permissions

### "File Not Found"

- Verify paths in `config.php` are correct
- Use double backslashes: `H:\\Path\\To\\File`
- Check drive letters are correct
- Ensure directories exist

### Server Won't Start

- Check batch file exists and is correct
- Verify ARK server executable path
- Check server isn't already running
- Review batch file for errors

### Character Transfer Failed

- Ensure both servers are stopped
- Check save directories exist
- Verify player files exist in source
- Check write permissions on target directory

## 📊 Logs

Manager actions are logged to: `H:\ark-manager\logs\manager.log`

Log entries include:
- User IP address
- Timestamp
- Action performed
- Details/parameters

## 🔄 Updating

To update the manager:

1. Backup your `config.php`
2. Replace all files except `config.php` and `.htpasswd`
3. Clear browser cache
4. Test functionality

## 📞 Support

Common issues and solutions:

- **Batch files** - Ensure no spaces in critical paths
- **RCON** - Must enable in server launch parameters
- **Permissions** - Apache user needs full access to ARK directories
- **Paths** - Always use absolute paths with double backslashes

## ⚡ Performance Tips

- Close server logs when not needed (reduces load)
- Use "Force Kill" only when necessary
- Schedule regular world saves via RCON
- Keep backups of INI files before major changes
- Monitor server RAM usage

## 📜 License

This is a custom tool for personal server management. Use at your own risk.

## 🙏 Credits

Created for ARK server administrators who want an easy-to-use web interface for server management.

---

**Version:** 1.0  
**Last Updated:** 2025  
**Compatible With:** ARK: Survival Evolved Dedicated Server (Windows)