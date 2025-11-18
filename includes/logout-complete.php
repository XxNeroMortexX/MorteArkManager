<?php
/**
 * Logout Complete - Shows after credentials cleared
 */

// Send 401 to ensure auth is cleared
header('HTTP/1.1 401 Unauthorized');
header('WWW-Authenticate: Basic realm="Logged Out"');

// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logged Out - ARK Manager</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body { 
            background: #1a1a2e; 
            color: #e0e0e0; 
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .logout-box {
            background: #0f3460;
            padding: 3rem;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #4CAF50;
            max-width: 500px;
        }
        h1 { color: #4CAF50; margin-bottom: 1rem; }
        p { margin: 1rem 0; line-height: 1.6; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 1rem;
            font-weight: bold;
        }
        .btn:hover { background: #45a049; }
        .note { 
            font-size: 0.9rem; 
            color: #999;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #2a2a3e;
        }
    </style>
</head>
<body>
    <div class="logout-box">
        <h1>✅ Logged Out Successfully</h1>
        <p>You have been logged out of ARK Server Manager.</p>
        <p>Your browser credentials have been cleared.</p>
        
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']) . '/..'; ?>/" class="btn">
		🔑 Login Again
		</a>
        
        <div class="note">
            <strong>Note:</strong> If you're still logged in after clicking login, 
            please close this browser tab/window and open a new one.
        </div>
    </div>
    
    <script>
        // Clear any cached credentials
        if (window.stop !== undefined) {
            window.stop();
        } else if (document.execCommand !== undefined) {
            document.execCommand("Stop", false);
        }
    </script>
</body>
</html>