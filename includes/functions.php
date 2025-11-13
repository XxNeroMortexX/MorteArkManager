<?php
/**
 * Common functions for ARK Manager
 */

if (!defined('ARK_MANAGER')) {
    die('Direct access not permitted');
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get base URL for the application
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace('\\', '/', dirname($script));
    
    // Remove trailing slash if exists
    $path = rtrim($path, '/');
    
    // If we're in a subdirectory, get the base path
    if (strpos($path, '/') !== false) {
        $parts = explode('/', $path);
        // Get up to the ark-manager directory
        $baseParts = [];
        foreach ($parts as $part) {
            $baseParts[] = $part;
            if (strpos($part, 'ark') !== false || $part === 'manager') {
                break;
            }
        }
        $path = implode('/', $baseParts);
    }
    
    return $protocol . '://' . $host . $path . '/';
}

/**
 * Redirect with message
 */
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION[$type] = $message;
    }
    header('Location: ' . $url);
    exit;
}

/**
 * JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Escape command for Windows
 */
function escapeWindowsCommand($command) {
    return '"' . str_replace('"', '""', $command) . '"';
}

/**
 * Execute command and return output
 */
function executeCommand($command, &$output = null, &$returnCode = null) {
    $output = [];
    $returnCode = 0;
    
    exec($command . ' 2>&1', $output, $returnCode);
    
    return [
        'success' => $returnCode === 0,
        'output' => implode("\n", $output),
        'code' => $returnCode
    ];
}

/**
 * Read last N lines from a file
 */
function tailFile($filepath, $lines = 50) {
    if (!file_exists($filepath)) {
        return [];
    }

    $file = new SplFileObject($filepath, 'r');
    $file->seek(PHP_INT_MAX);
    $lastLine = $file->key();
    $startLine = max(0, $lastLine - $lines);
    
    $result = [];
    $file->seek($startLine);
    
    while (!$file->eof()) {
        $line = $file->current();
        if ($line !== false && trim($line) !== '') {
            $result[] = rtrim($line);
        }
        $file->next();
    }
    
    return $result;
}

/**
 * Get directory tree
 */
function getDirectoryTree($path, $maxDepth = 3, $currentDepth = 0) {
    $tree = [];
    
    if ($currentDepth >= $maxDepth) {
        return $tree;
    }
    
    if (!is_dir($path)) {
        return $tree;
    }
    
    $items = @scandir($path);
    if ($items === false) {
        return $tree;
    }
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $fullPath = $path . DIRECTORY_SEPARATOR . $item;
        
        if (is_dir($fullPath)) {
            $tree[$item] = [
                'type' => 'directory',
                'path' => $fullPath,
                'children' => getDirectoryTree($fullPath, $maxDepth, $currentDepth + 1)
            ];
        } else {
            $tree[$item] = [
                'type' => 'file',
                'path' => $fullPath,
                'size' => filesize($fullPath),
                'modified' => filemtime($fullPath)
            ];
        }
    }
    
    return $tree;
}

/**
 * Parse ARK character profile filename
 */
function parseCharacterFile($filename) {
    // Format: SteamID.arkprofile or LocalPlayer.arkprofile
    $basename = basename($filename, '.arkprofile');
    
    if ($basename === 'LocalPlayer') {
        return [
            'steam_id' => 'LocalPlayer',
            'display_name' => 'Local Player'
        ];
    }
    
    return [
        'steam_id' => $basename,
        'display_name' => $basename
    ];
}

/**
 * Get player display name from Steam ID
 */
function getPlayerName($steamId) {
    global $PLAYERS;
    
    if (isset($PLAYERS[$steamId])) {
        return $PLAYERS[$steamId];
    }
    
    return $steamId;
}

/**
 * Get all character files from a save directory
 */
function getCharacterFiles($saveDir) {
    $savePath = ARK_SAVED_DIR . DIRECTORY_SEPARATOR . $saveDir;
    
    if (!is_dir($savePath)) {
        return [];
    }
    
    $files = glob($savePath . DIRECTORY_SEPARATOR . '*.arkprofile');
    $characters = [];
    
    foreach ($files as $file) {
        $info = parseCharacterFile($file);
        $characters[] = [
            'file' => $file,
            'filename' => basename($file),
            'steam_id' => $info['steam_id'],
            'display_name' => getPlayerName($info['steam_id']),
            'size' => filesize($file),
            'modified' => filemtime($file)
        ];
    }
    
    // Sort by display name
    usort($characters, function($a, $b) {
        return strcmp($a['display_name'], $b['display_name']);
    });
    
    return $characters;
}

/**
 * Validate server key exists
 */
function validateServer($serverKey) {
    global $SERVERS;
    return isset($SERVERS[$serverKey]);
}

/**
 * Get server info
 */
function getServerInfo($serverKey) {
    global $SERVERS;
    return $SERVERS[$serverKey] ?? null;
}

/**
 * Format bytes to human readable
 */
function humanFileSize($bytes, $decimals = 2) {
    $size = ['B', 'KB', 'MB', 'GB', 'TB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
}