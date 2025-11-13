<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>ARK Server Manager</title>
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>assets/css/style.css">
    <script src="<?php echo getBaseUrl(); ?>assets/js/main.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="<?php echo getBaseUrl(); ?>">🦖 ARK Manager</a>
            </div>
            <ul class="nav-menu">
                <?php if (hasPermission('view_dashboard')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>">Dashboard</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_ini_editor')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>ini-editor/">INI Editor</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_server_control')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>server-control/">Server Control</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_rcon')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>rcon/">RCON</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_character_transfer')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>character-transfer/">Characters</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_file_browser')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>file-browser/">Files</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_scripts')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>scripts/">Scripts</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_logs')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>logs/">Logs</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_system_monitor')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>monitor/">Monitor</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_manager_settings')): ?>
                <li><a href="<?php echo getBaseUrl(); ?>settings/">⚙️ Settings</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>