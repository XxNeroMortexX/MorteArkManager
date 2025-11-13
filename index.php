<?php
/**
 * ARK: Survival Evolved Web Manager
 * Dashboard / Main Page
 */

define('ARK_MANAGER', true);
require_once 'config.php';
require_once 'includes/functions.php';

// Check permission
if (!hasPermission('view_dashboard')) {
    die('Access denied');
}

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<div class="dashboard">
    <div class="welcome-section">
        <h1>🦖 ARK Server Manager</h1>
        <p>Welcome to the ARK: Survival Evolved Web Management Panel</p>
    </div>

    <div class="stats-grid">
        <?php foreach ($SERVERS as $key => $server): ?>
        <?php $serverRunning = isServerRunning($key); ?>
        <div class="stat-card <?php echo $serverRunning ? 'online' : 'offline'; ?>">
            <div class="stat-header">
                <h3><?php echo htmlspecialchars($server['map']); ?></h3>
                <span class="status-badge">
                    <?php echo $serverRunning ? '🟢 Online' : '🔴 Offline'; ?>
                </span>
            </div>
            <div class="stat-body">
                <p><strong>Server:</strong> <?php echo htmlspecialchars($server['name']); ?></p>
                <p><strong>Port:</strong> <?php echo $server['port']; ?></p>
                <p><strong>RCON Port:</strong> <?php echo $server['rcon_port']; ?></p>
                <p><strong>Save Directory:</strong> <?php echo htmlspecialchars($server['save_dir']); ?></p>
            </div>
            <div class="stat-footer">
                <a href="server-control/?server=<?php echo $key; ?>" class="btn btn-primary">
                    Manage Server
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-grid">
            <?php if (hasPermission('view_ini_editor')): ?>
            <a href="ini-editor/" class="action-card">
                <span class="icon">📝</span>
                <h3>Edit INI Files</h3>
                <p>Modify server configuration files</p>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('view_rcon')): ?>
            <a href="rcon/" class="action-card">
                <span class="icon">💻</span>
                <h3>RCON Console</h3>
                <p>Execute remote console commands</p>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('view_character_transfer')): ?>
            <a href="character-transfer/" class="action-card">
                <span class="icon">🔄</span>
                <h3>Transfer Characters</h3>
                <p>Move players between servers</p>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('view_file_browser')): ?>
            <a href="file-browser/" class="action-card">
                <span class="icon">📁</span>
                <h3>File Browser</h3>
                <p>Browse and edit server files</p>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('view_scripts')): ?>
            <a href="scripts/" class="action-card">
                <span class="icon">⚙️</span>
                <h3>Run Scripts</h3>
                <p>Execute batch files and scripts</p>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('view_logs')): ?>
            <a href="logs/" class="action-card">
                <span class="icon">📋</span>
                <h3>View Logs</h3>
                <p>Real-time server log viewer</p>
            </a>
            <?php endif; ?>
            
            <?php if (hasPermission('view_system_monitor')): ?>
            <a href="monitor/" class="action-card">
                <span class="icon">📊</span>
                <h3>System Monitor</h3>
                <p>CPU, RAM, Network usage</p>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="system-info">
        <h2>System Information</h2>
        <?php if (hasPermission('view_manager_settings')): ?>
            <div style="text-align: right; margin-bottom: 1rem;">
                <a href="settings/" class="btn btn-primary">⚙️ Edit Manager Settings</a>
            </div>
        <?php endif; ?>
        <div class="info-grid">
            <div class="info-card">
                <h4>ARK Root Directory</h4>
                <p><?php echo ARK_ROOT; ?></p>
            </div>
            <div class="info-card">
                <h4>Config Directory</h4>
                <p><?php echo ARK_CONFIG_DIR; ?></p>
            </div>
            <div class="info-card">
                <h4>Cluster Directory</h4>
                <p><?php echo CLUSTER_DIR; ?></p>
            </div>
            <div class="info-card">
                <h4>PHP Version</h4>
                <p><?php echo phpversion(); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
