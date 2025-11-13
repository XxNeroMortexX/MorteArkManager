<?php
/**
 * ARK: Survival Evolved Web Manager
 * Configuration File
 */

// Prevent direct access
if (!defined('ARK_MANAGER')) {
    die('Direct access not permitted');
}

// === SERVER PATHS ===
define('ARK_ROOT', 'C:\\ARKServers\\ARKASE');
define('ARK_EXECUTABLE', ARK_ROOT . '\\ShooterGame\\Binaries\\Win64\\ShooterGameServer.exe');
define('ARK_CONFIG_DIR', ARK_ROOT . '\\ShooterGame\\Saved\\Config\\WindowsServer');
define('ARK_SAVED_DIR', ARK_ROOT . '\\ShooterGame\\Saved');
define('ARK_LOGS_DIR', ARK_SAVED_DIR . '\\Logs');
define('CLUSTER_DIR', 'C:\\ARKServers');

// === BACKUP SETTINGS ===
define('BACKUP_DIR', 'C:\\ARKServers\\Backups');
define('BACKUP_DATE_FORMAT', 'Y-m-d_H-i-s'); // Format: 2025-01-15_14-30-00
define('BACKUP_PREFIX', 'backup_'); // Prefix for backup files

// === BATCH FILES (Your actual batch file locations) ===
$BATCH_FILES = [
    'extinction' => 'C:\\ARKServers\\start_extinction.bat',
    'fjordur' => 'C:\\ARKServers\\start_fjordur.bat',
];

// === SERVER CONFIGURATIONS ===
$SERVERS = [
    'extinction' => [
        'name' => 'Ark_Morte_Extinction',
        'map' => 'Extinction',
        'rcon_ip' => '192.168.1.3',
        'rcon_port' => 27030,
        'port' => 7789,
        'query_port' => 27025,
        'save_dir' => 'ExtinctionSave',
        'batch_file' => 'extinction',
        'window_title_contains' => 'Extinction' // Part of command line to identify this server
    ],
    'fjordur' => [
        'name' => 'Ark_Morte_Fjordur',
        'map' => 'Fjordur',
        'rcon_ip' => '192.168.1.3',
        'rcon_port' => 27020,
        'port' => 7779,
        'query_port' => 27015,
        'save_dir' => 'FjordurSave',
        'batch_file' => 'fjordur',
        'window_title_contains' => 'Fjordur'
    ]
];

// === PLAYER MAPPING ===
$PLAYERS = [
    '76561198110989933' => 'MorteLeggenda',
    '76561198044632514' => 'alreyan',
    '76561198886216512' => 'agent3330dan',
    '76561198002595683' => 'CaptainPicard',
    '76561198023932657' => 'eonspam',
    '76561198029881028' => 'syphallis',
    '76561198061332625' => 'The Lizard King',
    '76561198069991521' => 'tree_frog',
    '76561198070980639' => 'Simp 4 Trickster',
    '76561198089266764' => 'Lollercaust',
    '76561198176448591' => 'toler1450',
    '76561198260025302' => 'Arkangel',
    '76561198299641171' => 'Reaper',
    '76561198339428654' => 'Escanor21',
    '76561198394199496' => 'alexander11730',
    '76561198798640660' => 'Balian of Ibelin',
    '76561198855859948' => 'moshi',
    '76561199009150942' => 'elainah',
    '76561199170340543' => 'LadyKing',
    '76561199358069652' => 'leo.alrich.ang',
    '76561199367430693' => 'tyvanray',
    '76561199465329364' => 'roshamti875567',
    '76561199543835678' => 'Essei',
    '76561199789114658' => 'alphonse.elric.a',
    '76561199805913265' => 'anna_bot_',
    '76561198285636776' => 'lynnpaskett'
];

// === INI FILES ===
$INI_FILES = [
    'GameUserSettings.ini' => ARK_CONFIG_DIR . '\\GameUserSettings.ini',
    'Game.ini' => ARK_CONFIG_DIR . '\\Game.ini',
    'Engine.ini' => ARK_CONFIG_DIR . '\\Engine.ini',
];

// === FILE BROWSER SETTINGS ===
// Define specific directories users can access per role
$FILE_BROWSER_ROOTS = [
    'Admin' => [
        'Full System' => 'C:\\',
        'ARK Servers' => 'C:\\ARKServers',
		'Config Files' => ARK_CONFIG_DIR,
        'Logs' => ARK_LOGS_DIR,
		'Save Files' => ARK_SAVED_DIR,
    ],
    'Moderator' => [
        'ARK Servers' => 'C:\\ARKServers',
        'Config Files' => ARK_CONFIG_DIR,
        'Logs' => ARK_LOGS_DIR,
    ],
    'Player' => [
        'Save Files' => ARK_SAVED_DIR,
    ]
];

// Allowed file extensions to VIEW
$ALLOWED_EXTENSIONS = ['ini', 'txt', 'log', 'json', 'cfg', 'bat', 'sh', 'py', 'xml'];

// Allowed file extensions to EDIT
$EDITABLE_EXTENSIONS = ['ini', 'txt', 'json', 'cfg', 'bat', 'sh', 'py'];

// Hidden file extensions (won't show in file browser)
$HIDDEN_EXTENSIONS = ['exe', 'dll', 'sys', 'dat', 'tmp'];

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

// === INI EDITOR - LIMITED KEYS ===
// Define which INI keys certain roles can edit (Admin can edit all)
$INI_EDITABLE_KEYS = [
    'Admin' => [
        'GameUserSettings.ini' => [
            'DifficultyOffset',
            'TamingSpeedMultiplier',
            'HarvestAmountMultiplier',
            'XPMultiplier',
            'Message',
        ],
    ],
    'player' => [
        // Players can't edit any keys by default
    ]
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
 * - view_manager_settings: Can edit manager config (settings page)
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
 * Logs:
 * 
 * Monitor:
 * 
 * Special:
 * - all: Has all permissions (Admin only)
 */

$USER_ROLES = [
    'Admin' => [
        'name' => 'Administrator',
        // Admin has 'all' permissions
		'permissions' => [
            'view_dashboard',
            'view_ini_editor',
            'all',
        ],
    ],
    'Moderator' => [
        'name' => 'Moderator',
        'permissions' => [
            'view_dashboard',
            'view_character_transfer',
            'download_characters',
            'view_logs',
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

// Map .htaccess usernames to roles
$USER_ROLE_MAPPING = [
    'Admin' => 'Admin',
    // Add more users here:
    // 'Admin1' => 'Admin',
    // 'moderator1' => 'Moderator',
    // 'player1' => 'Player',
];

// === HELPER FUNCTIONS ===

/**
 * Get current user's role
 */
function getCurrentUserRole() {
    global $USER_ROLE_MAPPING;
    
    $username = $_SERVER['PHP_AUTH_USER'] ?? 'guest';
    return $USER_ROLE_MAPPING[$username] ?? 'Player';
}

/**
 * Get current user's permissions
 */
function getUserPermissions() {
    global $USER_ROLES;
    
    $role = getCurrentUserRole();
    return $USER_ROLES[$role] ?? $USER_ROLES['Player'];
}

/**
 * Check if user has permission
 */
function hasPermission($permission) {
    $perms = getUserPermissions();
    
    if (in_array('all', $perms['permissions'])) {
        return true;
    }
    
    return in_array($permission, $perms['permissions']);
}

/**
 * Check if a server process is running by command line
 */
function isServerRunning($serverKey = null) {
    global $SERVERS;
    
    // Use wmic to get process command line (works from services)
    $command = 'wmic process where "name=\'ShooterGameServer.exe\'" get CommandLine /format:list 2>&1';
    
    $output = [];
    exec($command, $output, $returnCode);
    
    if (empty($output)) {
        return false;
    }
    
    // Combine output into full text
    $fullOutput = implode("\n", $output);
    
    // If checking for specific server
    if ($serverKey !== null && isset($SERVERS[$serverKey])) {
        $searchTerm = $SERVERS[$serverKey]['window_title_contains'];
        
        // Check if the command line contains our search term
        return stripos($fullOutput, $searchTerm) !== false;
    }
    
    // Just check if any ARK server is running
    return stripos($fullOutput, 'ShooterGameServer') !== false;
}

/**
 * Get server PID by command line search
 */
function getServerPID($serverKey = null) {
    global $SERVERS;
    
    if ($serverKey === null) {
        // Get any ARK process PID
        $output = [];
        exec('tasklist /FI "IMAGENAME eq ShooterGameServer.exe" /FO CSV /NH 2>&1', $output);
        
        if (!empty($output)) {
            $parts = str_getcsv($output[0]);
            return isset($parts[1]) ? trim($parts[1]) : null;
        }
        return null;
    }
    
    // Get specific server PID by command line
    $searchTerm = $SERVERS[$serverKey]['window_title_contains'];
    
    // Get all ShooterGameServer processes with their PIDs and command lines
    $command = 'wmic process where "name=\'ShooterGameServer.exe\'" get ProcessId,CommandLine /format:csv 2>&1';
    
    $output = [];
    exec($command, $output);
    
    foreach ($output as $line) {
        // Skip empty lines and headers
        if (empty(trim($line)) || stripos($line, 'Node,CommandLine') !== false) {
            continue;
        }
        
        if (stripos($line, $searchTerm) !== false) {
            // Extract PID from CSV format: Node,CommandLine,ProcessId
            $parts = str_getcsv($line);
            // PID is the last element
            $pid = trim($parts[count($parts) - 1]);
            // Make sure it's numeric
            if (is_numeric($pid)) {
                return $pid;
            }
        }
    }
    
    return null;
}

/**
 * Get full server window title and info
 */
function getServerWindowTitle($serverKey) {
    global $SERVERS;
    
    $searchTerm = $SERVERS[$serverKey]['window_title_contains'];
    
    // Get command line which contains the map and settings
    $command = 'wmic process where "name=\'ShooterGameServer.exe\'" get CommandLine /format:list 2>&1';
    
    $output = [];
    exec($command, $output);
    
    $fullOutput = implode("\n", $output);
    
    // Look for our specific server in the output
    if (stripos($fullOutput, $searchTerm) !== false) {
        // Extract relevant info from command line
        preg_match('/(' . preg_quote($searchTerm, '/') . '[^\n]*)/i', $fullOutput, $matches);
        if (!empty($matches[1])) {
            // Clean up the output
            $info = trim($matches[1]);
            // Limit length for display
            if (strlen($info) > 150) {
                $info = substr($info, 0, 150) . '...';
            }
            return $info;
        }
    }
    
    return null;
}

/**
 * Create backup with custom naming
 */
function createBackup($filePath, $type = 'file') {
    if (!file_exists($filePath)) {
        return false;
    }
    
    // Create backup directory if it doesn't exist
    if (!is_dir(BACKUP_DIR)) {
        mkdir(BACKUP_DIR, 0755, true);
    }
    
    $filename = basename($filePath);
    $timestamp = date(BACKUP_DATE_FORMAT);
    $backupName = BACKUP_PREFIX . $type . '_' . $filename . '_' . $timestamp;
    $backupPath = BACKUP_DIR . DIRECTORY_SEPARATOR . $backupName;
    
    return copy($filePath, $backupPath) ? $backupPath : false;
}

/**
 * RCON password from INI
 */
function getRconPassword() {
    global $INI_FILES;
    $iniFile = $INI_FILES['GameUserSettings.ini'];
    
    if (!file_exists($iniFile)) {
        return null;
    }
    
    $content = file_get_contents($iniFile);
    if (preg_match('/ServerAdminPassword=(.+)$/m', $content, $matches)) {
        return trim($matches[1]);
    }
    
    return null;
}

/**
 * Check if path is safe (within allowed directories)
 */
function isSafePath($path) {
    global $FILE_BROWSER_ROOTS;
    
    $role = getCurrentUserRole();
    $allowedRoots = $FILE_BROWSER_ROOTS[$role] ?? [];
    
    $realPath = realpath($path);
    if ($realPath === false) {
        return false;
    }
    
    // Check if path is within any allowed root for this role
    foreach ($allowedRoots as $name => $root) {
        $realRoot = realpath($root);
        if ($realRoot && strpos($realPath, $realRoot) === 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Check if file extension is allowed to view
 */
function isAllowedExtension($filename) {
    global $ALLOWED_EXTENSIONS, $HIDDEN_EXTENSIONS;
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    // Check if hidden
    if (in_array($ext, $HIDDEN_EXTENSIONS)) {
        return false;
    }
    
    return in_array($ext, $ALLOWED_EXTENSIONS);
}

/**
 * Check if file is editable
 */
function isEditableFile($filename) {
    global $EDITABLE_EXTENSIONS;
    
    if (!hasPermission('edit_files') && !hasPermission('all')) {
        return false;
    }
    
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $EDITABLE_EXTENSIONS);
}

/**
 * Log action
 */
function logAction($action, $details = '') {
    $logFile = __DIR__ . '/logs/manager.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $user = $_SERVER['PHP_AUTH_USER'] ?? 'unknown';
    $entry = "[$timestamp] [$user@$ip] $action";
    
    if ($details) {
        $entry .= " - $details";
    }
    
    $entry .= "\n";
    
    file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}