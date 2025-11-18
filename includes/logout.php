<?php
/**
 * Logout - Forces HTTP Basic Auth to re-prompt
 */

// Send 401 Unauthorized to force browser to forget credentials
header('HTTP/1.1 401 Unauthorized');
header('WWW-Authenticate: Basic realm="ARK Server Manager - Logged Out"');

// Clear PHP auth variables
unset($_SERVER['PHP_AUTH_USER']);
unset($_SERVER['PHP_AUTH_PW']);

// Destroy session if any
session_start();
session_destroy();

// Get the current host and path
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['SCRIPT_NAME']);

// Redirect to a URL with invalid credentials to force logout
// Format: http://logout:logout@hostname/path
$logoutUrl = $protocol . '://logout:logout@' . $host . $path . '/logout-complete.php';

// Redirect immediately
header('Location: ' . $logoutUrl);
exit;
?>