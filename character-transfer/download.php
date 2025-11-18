<?php
/**
 * Character File Download Handler
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check permissions
$perms = getUserPermissions();
if (!$perms['can_download_characters']) {
    die('Access denied');
}

$server = $_GET['server'] ?? '';
$filename = $_GET['file'] ?? '';

if (!validateServer($server)) {
    die('Invalid server');
}

$serverInfo = getServerInfo($server);
$savePath = ARK_SAVED_DIR . DIRECTORY_SEPARATOR . $serverInfo['save_dir'];
$filePath = $savePath . DIRECTORY_SEPARATOR . basename($filename);

// Security check
if (!file_exists($filePath) || !isSafePath($filePath)) {
    die('File not found or access denied');
}

// Only allow .arkprofile and .arktribe files
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
if (!in_array($ext, ['arkprofile', 'arktribe'])) {
    die('Invalid file type');
}

// Log the download
logAction('CHARACTER_DOWNLOAD', basename($filePath) . ' from ' . $serverInfo['map']);

// Send file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

readfile($filePath);
exit;