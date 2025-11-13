<?php
/**
 * Manager Settings Editor (Admin Only)
 */

//Check Server Permissions
//echo '<pre>';
//print_r($_SERVER);
//echo '</pre>';

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!hasPermission('view_manager_settings')) {
    die('Access denied. Admin only.');
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $configFile = __DIR__ . '/../config.php';
    $originalContent = file_get_contents($configFile);
    
    // Create backup
    $backupPath = createBackup($configFile, 'config');
    
    $updates = [];
    
    // Update ARK Root
    if (isset($_POST['ark_root'])) {
        $arkRoot = str_replace('\\', '\\\\', $_POST['ark_root']);
        $originalContent = preg_replace(
            "/define\('ARK_ROOT', '.*?'\);/",
            "define('ARK_ROOT', '$arkRoot');",
            $originalContent
        );
        $updates[] = 'ARK_ROOT';
    }
    
    // Update Backup Directory
    if (isset($_POST['backup_dir'])) {
        $backupDir = str_replace('\\', '\\\\', $_POST['backup_dir']);
        $originalContent = preg_replace(
            "/define\('BACKUP_DIR', '.*?'\);/",
            "define('BACKUP_DIR', '$backupDir');",
            $originalContent
        );
        $updates[] = 'BACKUP_DIR';
    }
    
    // Update Backup Date Format
    if (isset($_POST['backup_date_format'])) {
        $format = $_POST['backup_date_format'];
        $originalContent = preg_replace(
            "/define\('BACKUP_DATE_FORMAT', '.*?'\);/",
            "define('BACKUP_DATE_FORMAT', '$format');",
            $originalContent
        );
        $updates[] = 'BACKUP_DATE_FORMAT';
    }
    
    // Update Backup Prefix
    if (isset($_POST['backup_prefix'])) {
        $prefix = $_POST['backup_prefix'];
        $originalContent = preg_replace(
            "/define\('BACKUP_PREFIX', '.*?'\);/",
            "define('BACKUP_PREFIX', '$prefix');",
            $originalContent
        );
        $updates[] = 'BACKUP_PREFIX';
    }
    
    // Save the updated config
    if (file_put_contents($configFile, $originalContent)) {
        logAction('CONFIG_UPDATE', 'Updated: ' . implode(', ', $updates));
        $_SESSION['success'] = 'Configuration saved successfully! Backup created at: ' . basename($backupPath);
    } else {
        $_SESSION['error'] = 'Failed to save configuration';
    }
    
    redirect('./');
}

$pageTitle = 'Manager Settings';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>⚙️ Manager Settings</h1>
    <p>Configure ARK Manager settings (Admin Only)</p>
</div>

<div class="alert alert-warning">
    ⚠️ <strong>Warning:</strong> Incorrect settings can break the manager. A backup is created automatically before saving.
</div>

<div class="settings-container">
    <form method="POST" class="settings-form">
        <input type="hidden" name="action" value="save">
        
        <div class="settings-section">
            <h2>📁 Directory Paths</h2>
            
            <div class="form-group">
                <label>ARK Server Root Directory:</label>
                <input type="text" 
                       name="ark_root" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars(str_replace('\\\\', '\\', ARK_ROOT)); ?>"
                       placeholder="C:\ARKServers\ARKASE">
                <small style="color: #999;">Full path to your ARK server installation</small>
            </div>
        </div>

        <div class="settings-section">
            <h2>💾 Backup Settings</h2>
            
            <div class="form-group">
                <label>Backup Directory:</label>
                <input type="text" 
                       name="backup_dir" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars(BACKUP_DIR); ?>"
                       placeholder="C:\ARKServers\Backups">
                <small style="color: #999;">Where backup files are stored</small>
            </div>
            
            <div class="form-group">
                <label>Backup Date Format:</label>
                <input type="text" 
                       name="backup_date_format" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars(BACKUP_DATE_FORMAT); ?>"
                       placeholder="Y-m-d_H-i-s">
                <small style="color: #999;">PHP date format (e.g., Y-m-d_H-i-s = 2025-01-15_14-30-00)</small>
            </div>
            
            <div class="form-group">
                <label>Backup Filename Prefix:</label>
                <input type="text" 
                       name="backup_prefix" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars(BACKUP_PREFIX); ?>"
                       placeholder="backup_">
                <small style="color: #999;">Prefix added to backup filenames</small>
            </div>
        </div>

        <div class="settings-section">
            <h2>ℹ️ Current Configuration</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h4>Config Directory</h4>
                    <p><?php echo htmlspecialchars(ARK_CONFIG_DIR); ?></p>
                </div>
                <div class="info-card">
                    <h4>Saved Files Directory</h4>
                    <p><?php echo htmlspecialchars(ARK_SAVED_DIR); ?></p>
                </div>
                <div class="info-card">
                    <h4>Logs Directory</h4>
                    <p><?php echo htmlspecialchars(ARK_LOGS_DIR); ?></p>
                </div>
                <div class="info-card">
                    <h4>Cluster Directory</h4>
                    <p><?php echo htmlspecialchars(CLUSTER_DIR); ?></p>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success btn-lg">💾 Save Configuration</button>
            <a href="../" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>

    <div class="settings-section">
        <h2>📝 Manual Configuration</h2>
        <p>For advanced settings, edit <code>config.php</code> directly:</p>
        <ul>
            <li>Server configurations (ports, IPs, save directories)</li>
            <li>Player Steam ID mappings</li>
            <li>RCON and Scripts quick commands</li>
            <li>File browser allowed directories</li>
            <li>User roles and permissions</li>
            <li>Allowed/hidden file extensions</li>
        </ul>
        <a href="../file-browser/?root=ARK Root&view=config.php" class="btn btn-info">
            📄 View config.php in File Browser
        </a>
    </div>
</div>

<style>
.settings-container {
    max-width: 900px;
    margin: 0 auto;
}

.settings-section {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
}

.settings-section h2 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    padding-top: 2rem;
}
</style>

<?php include '../includes/footer.php'; ?>
