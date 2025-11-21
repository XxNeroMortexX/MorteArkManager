<?php

// Map .htaccess usernames to roles
$USER_ROLE_MAPPING = [
    'Admin' => 'Admin',
    'Morte' => 'Moderator',
    // Add more users here:
    // 'moderator1' => 'Moderator',
    // 'player1' => 'Player',
];

// === USER PERMISSIONS SYSTEM ===
/**
 * AVAILABLE PERMISSIONS:
 * 
 * Page Access:
 * - view_dashboard: Can see dashboard
 * - view_ini_editor: Can access INI editor page
 * - view_server_control: Can access server control page
 * - view_rcon: Can access RCON console page
 * - view_character_transfer: Can access character transfer page
 * - view_file_browser: Can access file browser page
 * - view_scripts: Can access scripts page
 * - view_logs: Can access logs page
 * - view_system_monitor: Can access system monitor page
 * - edit_manager_settings: Can edit manager config (settings page)
 * 
 * INI Editor:
 * - edit_ini_gameusersettings: Can edit GameUserSettings.ini
 * - edit_ini_game: Can edit Game.ini
 * - edit_ini_engine: Can edit Engine.ini
 * - edit_ini_limited: Can only edit specific keys (defined in $INI_EDITABLE_KEYS)
 * 
 * Server Control:
 * - start_servers: Can start servers
 * - stop_servers: Can stop servers
 * - restart_servers: Can restart servers
 * - kill_servers: Can force kill server processes
 * 
 * RCON:
 * - execute_rcon: Can execute RCON commands
 * - execute_rcon_dangerous: Can execute dangerous commands (DoExit, kick, ban)
 * - execute_rcon_kick_players: Can kick players via dropdown (view and use kick player interface)
 * 
 * Character Transfer:
 * - transfer_characters: Can transfer characters between servers
 * - download_characters: Can download character backups
 * 
 * File Browser:
 * - edit_files: Can edit files in file browser
 * - delete_files: Can delete files
 * - file_browser_readonly: Can only view, not edit
 * 
 * Scripts:
 * - run_batch_scripts: Can run batch files
 * - run_custom_commands: Can type and execute custom commands
 * - run_quick_commands: Can use quick command buttons
 * 
 * Special:
 * - all: Has all permissions (Admin only)
 */

$USER_ROLES = [
    'Admin' => [
        'name' => 'Administrator',
        // Admin has 'all' permissions
		'permissions' => [
            'all',
        ],
    ],
    'Moderator' => [
        'name' => 'Moderator',
        'permissions' => [
            'view_dashboard',
            'view_server_control',
            'view_rcon',
            'execute_rcon',
            'view_character_transfer',
            'view_logs',
            'view_system_monitor',
            'download_characters',
        ],
    ],
    'Player' => [
        'name' => 'Player',
        'permissions' => [
            'view_dashboard',
            'view_character_transfer',
            'download_characters',
            'view_logs',
        ],
    ],
];

// === FILE BROWSER SETTINGS ===
// Define specific directories users can access per role
$FILE_BROWSER_ROOTS = [
    'Admin' => [
        'ARK Servers' => 'H:\\ARKServers',
		'Config Files' => ARK_CONFIG_DIR,
        'Logs' => ARK_LOGS_DIR,
		'Save Files' => ARK_SAVED_DIR,
    ],
    'Moderator' => [
        'Logs' => ARK_LOGS_DIR,
    ],
    'Player' => [
        'Logs' => ARK_LOGS_DIR,
    ]
];

// === RCON QUICK COMMANDS ===
// Add custom quick commands that appear on RCON page
$RCON_QUICK_COMMANDS = [
    'Save World' => 'SaveWorld',
    'List Players' => 'ListPlayers',
    'Destroy Wild Dinos' => 'DestroyWildDinos',
    'Get Chat' => 'GetChat',
    'Set Day' => 'SetTimeOfDay 12:00:00',
    'Set Night' => 'SetTimeOfDay 00:00:00',
];

// === RCON Quick Commands Permissions ===
$RCON_QUICK_Permissions = [
    'Admin' => [
		'Save World',
        'List Players',
		'Destroy Wild Dinos',
        'Get Chat',
		'Set Day',
		'Set Night',
    ],
    'Moderator' => [
        'Save World',
        'List Players',
		'Destroy Wild Dinos',
        'Get Chat',
		'Set Day',
		'Set Night',
    ],
    'Player' => [
        '',
    ]
];

// === SCRIPTS QUICK COMMANDS ===
// Add custom quick commands for Scripts page
$SCRIPT_QUICK_COMMANDS = [
    'Check ARK Process' => 'tasklist /FI "IMAGENAME eq ShooterGameServer.exe"',
    'Check Port 7779' => 'netstat -ano | findstr :7779',
    'Check Port 7789' => 'netstat -ano | findstr :7789',
    'Memory Info' => 'systeminfo | findstr /C:"Total Physical Memory" /C:"Available Physical Memory"',
    'Disk Space' => 'wmic logicaldisk get caption,freespace,size',
    'Network Info' => 'ipconfig',
];


// === INI EDITOR - VISIBLE KEYS PER ROLE ===
$INI_VISIBLE_KEYS = [
    'Admin' => [
        'GameUserSettings.ini' => 'ALL',
        'Game.ini' => 'ALL',
        'Engine.ini' => 'ALL',
    ],
	'Blank_Example' => [
        'GameUserSettings.ini' => [
            'DifficultyOffset',
            'TamingSpeedMultiplier',
            'HarvestAmountMultiplier',
            'XPMultiplier',
            'AutoSavePeriodMinutes',
        ],
        'Game.ini' => [
            'OverrideStructurePlatformPrevention',
            'MaxDifficulty',
        ],
        'Engine.ini' => [], // Can't see any Engine.ini keys
    ],
    'Player' => [
    ],
];


// IFs to Check Permissions
//
// if (hasPermission('view_dashboard')):
// endif;
// 
// // Check permission
// if (!hasPermission('view_dashboard')) {
//     die('Access denied');
// }


// Register all your configures (add more as needed)
// These will be checked by hasAccess() function
registerPermissionConfig('user_roles', $USER_ROLES);
registerPermissionConfig('file_browser_roots', $FILE_BROWSER_ROOTS);
registerPermissionConfig('rcon_quick_commands', $RCON_QUICK_COMMANDS);
registerPermissionConfig('script_quick_commands', $SCRIPT_QUICK_COMMANDS);
registerPermissionConfig('ini_visible_keys', $INI_VISIBLE_KEYS);
registerPermissionConfig('rcon_quick_permissions', $RCON_QUICK_Permissions);

//Other Arrays for Possible Permission Checking.
registerPermissionConfig('allowed_extensions', $ALLOWED_EXTENSIONS);
registerPermissionConfig('editable_extensions', $EDITABLE_EXTENSIONS);
registerPermissionConfig('hidden_extensions', $HIDDEN_EXTENSIONS);
registerPermissionConfig('batch_files', $BATCH_FILES);
registerPermissionConfig('servers', $SERVERS);
registerPermissionConfig('players', $PLAYERS);

?>