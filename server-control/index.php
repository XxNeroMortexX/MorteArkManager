<?php
/**
 * Server Control Panel
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../includes/rcon.php';

// Check permission
if (!hasPermission('view_server_control')) {
    die('Access denied');
}

// Handle server actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $server = $_POST['server'] ?? '';
    
    if (!validateServer($server)) {
        $_SESSION['error'] = 'Invalid server selected';
        redirect('./');
    }
    
    $serverInfo = getServerInfo($server);
    
    switch ($action) {
        case 'start':
            $batchFile = $BATCH_FILES[$serverInfo['batch_file']] ?? null;
            
            if (!$batchFile || !file_exists($batchFile)) {
                $_SESSION['error'] = 'Batch file not found';
                break;
            }
            
            // Start the server
            $command = 'start "" "' . $batchFile . '"';
            pclose(popen($command, 'r'));
            
            logAction('SERVER_START', $serverInfo['name']);
            $_SESSION['success'] = 'Server start command sent. Please wait 30-60 seconds for startup.';
            break;
            
        case 'stop':
            // Save world first via RCON
            $rconPassword = getRconPassword();
            
            if ($rconPassword) {
                $result = executeRCON(
                    $serverInfo['rcon_ip'],
                    $serverInfo['rcon_port'],
                    $rconPassword,
                    'SaveWorld'
                );
                
                sleep(2); // Wait for save to complete
                
                // Send DoExit command
                $result = executeRCON(
                    $serverInfo['rcon_ip'],
                    $serverInfo['rcon_port'],
                    $rconPassword,
                    'DoExit'
                );
                
                if ($result['success']) {
                    logAction('SERVER_STOP', $serverInfo['name']);
                    $_SESSION['success'] = 'Server shutdown command sent. World saved.';
                } else {
                    $_SESSION['error'] = 'Failed to send shutdown command: ' . $result['error'];
                }
            } else {
                $_SESSION['error'] = 'RCON password not configured';
            }
            break;
            
        case 'kill':
            // Force kill the process
            $output = [];
            exec('taskkill /F /IM ShooterGameServer.exe 2>&1', $output, $returnCode);
            
            if ($returnCode === 0) {
                logAction('SERVER_KILL', $serverInfo['name']);
                $_SESSION['success'] = 'Server process forcefully terminated';
            } else {
                $_SESSION['error'] = 'Failed to kill process: ' . implode("\n", $output);
            }
            break;
            
        case 'restart':
            // Save, stop, wait, start
            $rconPassword = getRconPassword();
            
            if ($rconPassword) {
                // Save world
                executeRCON(
                    $serverInfo['rcon_ip'],
                    $serverInfo['rcon_port'],
                    $rconPassword,
                    'SaveWorld'
                );
                
                sleep(2);
                
                // Shutdown
                executeRCON(
                    $serverInfo['rcon_ip'],
                    $serverInfo['rcon_port'],
                    $rconPassword,
                    'DoExit'
                );
                
                sleep(5);
                
                // Start
                $batchFile = $BATCH_FILES[$serverInfo['batch_file']] ?? null;
                if ($batchFile && file_exists($batchFile)) {
                    $command = 'start "" "' . $batchFile . '"';
                    pclose(popen($command, 'r'));
                    
                    logAction('SERVER_RESTART', $serverInfo['name']);
                    $_SESSION['success'] = 'Server restarting. Please wait 30-60 seconds.';
                }
            } else {
                $_SESSION['error'] = 'RCON password not configured';
            }
            break;
    }
    
    redirect('./?server=' . urlencode($server));
}

// Get selected server
$selectedServer = $_GET['server'] ?? 'extinction';
if (!validateServer($selectedServer)) {
    $selectedServer = 'extinction';
}

$serverInfo = getServerInfo($selectedServer);
$isRunning = isServerRunning($selectedServer);
$pid = getServerPID($selectedServer);
$windowTitle = getServerWindowTitle($selectedServer);

$pageTitle = 'Server Control';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>🎮 Server Control</h1>
    <p>Start, stop, and manage ARK servers</p>
</div>

<div class="server-control-container">
    <div class="server-selector">
        <h3>Select Server:</h3>
        <div class="server-tabs">
            <?php foreach ($SERVERS as $key => $server): ?>
                <a href="?server=<?php echo urlencode($key); ?>" 
                   class="server-tab <?php echo $selectedServer === $key ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($server['map']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="server-status-card">
        <h2><?php echo htmlspecialchars($serverInfo['map']); ?> Server</h2>
        
        <div class="status-indicator <?php echo $isRunning ? 'online' : 'offline'; ?>">
            <span class="status-dot"></span>
            <span class="status-text"><?php echo $isRunning ? 'ONLINE' : 'OFFLINE'; ?></span>
        </div>

        <div class="server-details">
            <div class="detail-row">
                <span class="label">Server Name:</span>
                <span class="value"><?php echo htmlspecialchars($serverInfo['name']); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Map:</span>
                <span class="value"><?php echo htmlspecialchars($serverInfo['map']); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Port:</span>
                <span class="value"><?php echo $serverInfo['port']; ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Query Port:</span>
                <span class="value"><?php echo $serverInfo['query_port']; ?></span>
            </div>
            <div class="detail-row">
                <span class="label">RCON Port:</span>
                <span class="value"><?php echo $serverInfo['rcon_port']; ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Save Directory:</span>
                <span class="value"><?php echo htmlspecialchars($serverInfo['save_dir']); ?></span>
            </div>
            <?php if ($pid): ?>
            <div class="detail-row">
                <span class="label">Process ID:</span>
                <span class="value"><?php echo $pid; ?></span>
            </div>
            <?php endif; ?>
            <?php if ($windowTitle): ?>
            <div class="detail-row">
                <span class="label">Window Title:</span>
                <span class="value" style="font-size: 0.85rem;" title="<?php echo htmlspecialchars($windowTitle); ?>">
                    <?php echo htmlspecialchars($windowTitle); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <div class="control-buttons">
            <?php if ($isRunning): ?>
                <form method="POST" style="display: inline;" onsubmit="return confirm('Stop the server? World will be saved first.');">
                    <input type="hidden" name="action" value="stop">
                    <input type="hidden" name="server" value="<?php echo htmlspecialchars($selectedServer); ?>">
                    <button type="submit" class="btn btn-warning">⏹️ Stop Server</button>
                </form>
                
                <form method="POST" style="display: inline;" onsubmit="return confirm('Restart the server? World will be saved first.');">
                    <input type="hidden" name="action" value="restart">
                    <input type="hidden" name="server" value="<?php echo htmlspecialchars($selectedServer); ?>">
                    <button type="submit" class="btn btn-info">🔄 Restart Server</button>
                </form>
                
                <form method="POST" style="display: inline;" onsubmit="return confirm('FORCE KILL the server process? This may cause data loss!');">
                    <input type="hidden" name="action" value="kill">
                    <input type="hidden" name="server" value="<?php echo htmlspecialchars($selectedServer); ?>">
                    <button type="submit" class="btn btn-danger">💀 Force Kill</button>
                </form>
            <?php else: ?>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="start">
                    <input type="hidden" name="server" value="<?php echo htmlspecialchars($selectedServer); ?>">
                    <button type="submit" class="btn btn-success">▶️ Start Server</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="batch-file-info">
            <h3>📄 Batch File</h3>
            <?php 
            $batchFile = $BATCH_FILES[$serverInfo['batch_file']] ?? null;
            if ($batchFile && file_exists($batchFile)):
            ?>
                <p><strong>File:</strong> <?php echo htmlspecialchars($batchFile); ?></p>
                <a href="../scripts/?run=<?php echo urlencode($serverInfo['batch_file']); ?>" class="btn btn-secondary">
                    View/Run Batch File
                </a>
            <?php else: ?>
                <p class="text-error">❌ Batch file not found</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-refresh status every 10 seconds
setTimeout(function() {
    location.reload();
}, 10000);
</script>

<?php include '../includes/footer.php'; ?>