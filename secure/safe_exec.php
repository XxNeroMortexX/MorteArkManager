<?php

/**
 * Safely Run ExeC Code without hackers attacking
 */
 
define('WEBROOT', '/ARK');
 
function safe_exec($command, &$output = null, &$returnCode = null) {
	
	// Add this to any Page that uses exec
	// require "/secure/safe_exec.php";
	// safe_exec("tasklist", $output, $code);

    // Allowed IPs
    //  $allowed_ips = [
    //      '127.0.0.1'
    //  ];
	//  
    //  if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    //      http_response_code(403);
    //      exit("Forbidden: IP not allowed " . $_SERVER['REMOTE_ADDR']);
    //  }

    // Allowed full file paths (not just file names)
    $allowed_pages = [
        WEBROOT . '/config.php',
        WEBROOT . '/includes/functions.php',
        WEBROOT . '/monitor/index.php',
        WEBROOT . '/scripts/index.php',
        WEBROOT . '/server-control/index.php',
        WEBROOT . '/index.php',
        WEBROOT . '/rcon/index.php',
    ];

    // Get the REAL script being executed
    $current_page = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);

    if (!in_array($current_page, $allowed_pages)) {
        http_response_code(403);
        exit("Forbidden: Page not allowed ");
        //exit("Forbidden: Page not allowed " . $current_page);
    }

    // Require Apache BasicAuth
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        http_response_code(403);
        exit("Forbidden: Authentication required " . $_SERVER['PHP_AUTH_USER']);
    }

    // Execute safely
    $result = exec($command . ' 2>&1', $output, $returnCode);

    return $result;
}

?>
