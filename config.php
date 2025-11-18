<?php

require_once "secure/safe_exec.php";
	
/**
 * ARK: Survival Evolved Web Manager
 * Configuration File
 */

// Prevent direct access
if (!defined('ARK_MANAGER')) {
    die('Direct access not permitted');
}

// === SERVER PATHS ===
define('ServerDrive', 'H:\\');
define('ARK_ROOT', ServerDrive . 'ARKServers\\ARKASE');
define('ARK_EXECUTABLE', ARK_ROOT . '\\ShooterGame\\Binaries\\Win64\\ShooterGameServer.exe');
define('ARK_CONFIG_DIR', ARK_ROOT . '\\ShooterGame\\Saved\\Config\\WindowsServer');
define('ARK_SAVED_DIR', ARK_ROOT . '\\ShooterGame\\Saved');
define('ARK_LOGS_DIR', ARK_SAVED_DIR . '\\Logs');
define('CLUSTER_DIR', ServerDrive . 'ARKServers');

// === BACKUP SETTINGS ===
define('BACKUP_DIR', ServerDrive . 'ARKServers\\Backups');
define('BACKUP_DATE_FORMAT', 'Y-m-d_H-i-s'); // Format: 2025-01-15_14-30-00
define('BACKUP_PREFIX', 'backup_'); // Prefix for backup files

// === BATCH FILES (Your actual batch file locations) ===
$BATCH_FILES = [
    'extinction' => ServerDrive . 'ARKServers\\start_extinction.bat',
    'fjordur' => ServerDrive . 'ARKServers\\start_fjordur.bat',
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

// Allowed file extensions to VIEW
$ALLOWED_EXTENSIONS = ['ini', 'txt', 'log', 'json', 'cfg', 'bat', 'sh', 'py', 'xml'];

// Allowed file extensions to EDIT
$EDITABLE_EXTENSIONS = ['ini', 'txt', 'json', 'cfg', 'bat', 'sh', 'py'];

// Hidden file extensions (won't show in file browser)
$HIDDEN_EXTENSIONS = ['exe', 'dll', 'sys', 'dat', 'tmp'];

require_once 'includes\keydescriptions.php';

// === HELPER FUNCTIONS ===


// === PERMISSION-BASED CONFIGURATION REGISTRY ===
// Register all role-based configuration arrays here
// Format: 'config_name' => &$variable_reference
$PERMISSION_CONFIGS = [];

// This function registers a config array for permission checking
function registerPermissionConfig($name, &$configArray) {
    global $PERMISSION_CONFIGS;
    $PERMISSION_CONFIGS[$name] = &$configArray;
}

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
 * Check if user has a specific permission
 */
function hasPermission($permission) {
    $perms = getUserPermissions();
    
    if (in_array('all', $perms['permissions'])) {
        return true;
    }
    
    return in_array($permission, $perms['permissions']);
}

/**
 * Check if user has access to a specific config item
 * Usage: hasAccess('file_browser_roots', 'ARK Servers')
 *        hasAccess('ini_visible_keys', 'GameUserSettings.ini')
 *        hasAccess('allowed_extensions', 'ini')
 */
function hasAccess($configName, $itemKey = null) {
    global $PERMISSION_CONFIGS;
    
    $role = getCurrentUserRole();
    
    // Check if config exists
    if (!isset($PERMISSION_CONFIGS[$configName])) {
        return false;
    }
    
    $config = $PERMISSION_CONFIGS[$configName];
    
    // If config is role-based (has role keys)
    if (isset($config[$role])) {
        $roleConfig = $config[$role];
        
        // If no specific item requested, return the whole role config
        if ($itemKey === null) {
            return $roleConfig;
        }
        
        // Check if item exists for this role
        if (is_array($roleConfig)) {
            // Handle 'ALL' special case
            if ($roleConfig === 'ALL') {
                return true;
            }
            
            // Check if key exists
            if (isset($roleConfig[$itemKey])) {
                return $roleConfig[$itemKey];
            }
            
            // Check if item is in array values (for simple arrays)
            if (in_array($itemKey, $roleConfig)) {
                return true;
            }
        } elseif ($roleConfig === 'ALL') {
            return true;
        }
        
        return false;
    }
    
    // If config is not role-based (flat array), check if item exists
    if ($itemKey !== null) {
        if (is_array($config)) {
            return isset($config[$itemKey]) || in_array($itemKey, $config);
        }
    }
    
    return $config;
}

/**
 * Get all accessible items from a config for current user
 * Usage: getAccessibleItems('file_browser_roots') returns all dirs user can access
 */
function getAccessibleItems($configName) {
    global $PERMISSION_CONFIGS;
    
    $role = getCurrentUserRole();
    
    if (!isset($PERMISSION_CONFIGS[$configName])) {
        return [];
    }
    
    $config = $PERMISSION_CONFIGS[$configName];
    
    // If role-based, return user's specific config
    if (isset($config[$role])) {
        return $config[$role];
    }
    
    // Otherwise return entire config (non role-based)
    return $config;
}

/**
 * Check if user can view a specific page/feature
 * This wraps hasPermission for consistency
 */
function canView($feature) {
    return hasPermission('view_' . $feature);
}

/**
 * Check if user can edit/modify something
 */
function canEdit($feature) {
    return hasPermission('edit_' . $feature) || hasPermission('all');
}

/**
 * Check if user can execute/run something
 */
function canExecute($feature) {
    return hasPermission('execute_' . $feature) || hasPermission('run_' . $feature) || hasPermission('all');
}

/**
 * Check if a server process is running by command line
 */
function isServerRunning($serverKey = null) {
    global $SERVERS;
    
    // Use wmic to get process command line (works from services)
    $command = 'wmic process where "name=\'ShooterGameServer.exe\'" get CommandLine /format:list 2>&1';
    
    $output = [];
    //exec($command, $output, $returnCode);
	safe_exec($command, $output, $returnCode);
    
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
        // Get any ARK process PID - simple and fast
        $output = [];
        //exec('wmic process where "name=\'ShooterGameServer.exe\'" get ProcessId /format:csv 2>&1', $output);
		safe_exec('wmic process where "name=\'ShooterGameServer.exe\'" get ProcessId /format:csv 2>&1', $output);
        
        foreach ($output as $line) {
            // Skip header and empty lines
            if (empty(trim($line)) || stripos($line, 'Node,ProcessId') !== false) {
                continue;
            }
            
            // Format: SERVERSYSTEM,14076
            $parts = explode(',', trim($line));
            if (count($parts) >= 2 && is_numeric($parts[1])) {
                return trim($parts[1]);
            }
        }
        return null;
    }
    
    // Get specific server PID by command line search
    $searchTerm = $SERVERS[$serverKey]['window_title_contains'];
    
    $command = 'wmic process where "name=\'ShooterGameServer.exe\'" get ProcessId,CommandLine /format:csv 2>&1';
    
    $output = [];
    //exec($command, $output);
	safe_exec($command, $output);
    
    foreach ($output as $line) {
        // Skip header and empty lines
        if (empty(trim($line)) || stripos($line, 'Node,CommandLine,ProcessId') !== false) {
            continue;
        }
        
        // Check if line contains our search term
        if (stripos($line, $searchTerm) !== false) {
            // Format: SERVERSYSTEM,"command line here",14076
            // Split by comma, but the command line might have commas in quotes
            $parts = str_getcsv($line);
            
            // PID is the last element
            if (count($parts) >= 3) {
                $pid = trim($parts[count($parts) - 1]);
                if (is_numeric($pid)) {
                    return $pid;
                }
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
    $command = 'wmic process where "name=\'ShooterGameServer.exe\'" get CommandLine 2>&1';
    
    $output = [];
    //exec($command, $output);
	safe_exec($command, $output);
    
    // Debug log
    logAction('DEBUG_TITLE', 'WMIC Output Lines: ' . count($output));
    
    $foundTitle = null;
    $inHeader = true;
    
    foreach ($output as $line) {
        $line = trim($line);
        
        // Skip empty lines
        if (empty($line)) {
            continue;
        }
        
        // Skip header
        if ($inHeader && stripos($line, 'CommandLine') !== false) {
            $inHeader = false;
            continue;
        }
        
        // Check if this line contains our search term
        if (stripos($line, $searchTerm) !== false) {
            // Found it! Clean it up for display
            $foundTitle = $line;
            
            // Try to extract just the map/session portion
            if (preg_match('/(' . preg_quote($searchTerm, '/') . '[^?]*)/i', $line, $matches)) {
                $foundTitle = $matches[1];
            }
            
            // Limit length
            if (strlen($foundTitle) > 200) {
                $foundTitle = substr($foundTitle, 0, 200) . '...';
            }
            
            logAction('DEBUG_TITLE', "Found title for $searchTerm: " . substr($foundTitle, 0, 100));
            break;
        }
    }
    
    return $foundTitle;
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

require_once 'Permissions.php';