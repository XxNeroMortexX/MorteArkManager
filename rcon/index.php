<?php
/**
 * RCON Console Interface
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../includes/rcon.php';

// Check permission
if (!hasPermission('view_rcon')) {
    die('Access denied');
}

// Handle RCON command execution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $server = $_POST['server'] ?? '';
    
    if (!validateServer($server)) {
        echo json_encode(['success' => false, 'error' => 'Invalid server']);
        exit;
    }
    
    $serverInfo = getServerInfo($server);
    $rconPassword = getRconPassword();
    
    if (!$rconPassword) {
        echo json_encode(['success' => false, 'error' => 'RCON password not configured']);
        exit;
    }
    
    if ($action === 'execute') {
        $command = $_POST['command'] ?? '';
        
        if (empty($command)) {
            echo json_encode(['success' => false, 'error' => 'Command cannot be empty']);
            exit;
        }
        
        $result = executeRCON(
            $serverInfo['rcon_ip'],
            $serverInfo['rcon_port'],
            $rconPassword,
            $command
        );
        
        if ($result['success']) {
            logAction('RCON_COMMAND', $command . ' on ' . $serverInfo['name']);
        }
        
        echo json_encode($result);
        exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

// Get selected server
$selectedServer = $_GET['server'] ?? 'extinction';
if (!validateServer($selectedServer)) {
    $selectedServer = 'extinction';
}

$serverInfo = getServerInfo($selectedServer);
$isRunning = isServerRunning($selectedServer);
$rconPassword = getRconPassword();

$pageTitle = 'RCON Console';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>💻 RCON Console</h1>
    <p>Execute remote console commands</p>
</div>

<div class="rcon-container">
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

    <div class="rcon-panel">
        <div class="panel-header">
            <h2><?php echo htmlspecialchars($serverInfo['map']); ?> Console</h2>
            <div class="server-status <?php echo $isRunning ? 'online' : 'offline'; ?>">
                <?php echo $isRunning ? '🟢 Online' : '🔴 Offline'; ?>
            </div>
        </div>

        <?php if (!$isRunning): ?>
            <div class="alert alert-warning" id="serverOfflineWarning">
                ⚠️ Server appears to be offline. RCON commands may fail.
            </div>
        <?php elseif (!$rconPassword): ?>
            <div class="alert alert-error">
                ❌ RCON password not configured in GameUserSettings.ini (ServerAdminPassword)
            </div>
        <?php endif; ?>

        <div class="quick-commands">
            <h3>Quick Commands:</h3>
            <div class="command-buttons">
                <?php
                global $RCON_QUICK_COMMANDS;
                if (hasPermission('execute_rcon')):
                    foreach ($RCON_QUICK_COMMANDS as $label => $command):
                        $isDangerous = (strpos($command, 'DoExit') !== false || strpos($command, 'Kick') !== false || strpos($command, 'Ban') !== false);
                        $btnClass = $isDangerous ? 'btn-warning' : 'btn-secondary';
                        
                        if ($isDangerous && !hasPermission('execute_rcon_dangerous') && !hasPermission('all')):
                            continue; // Skip dangerous commands for non-admins
                        endif;
                ?>
                    <button class="btn btn-sm <?php echo $btnClass; ?>" onclick="executeQuickCommand('<?php echo htmlspecialchars($command, ENT_QUOTES); ?>', <?php echo $isDangerous ? 'true' : 'false'; ?>)">
                        <?php echo htmlspecialchars($label); ?>
                    </button>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
        </div>

        <div class="console-output" id="consoleOutput">
            <div class="console-line">RCON Console Ready. Type a command below or use quick commands.</div>
        </div>

        <div class="console-input-container">
            <?php if (hasPermission('run_custom_commands') || hasPermission('all')): ?>
            <form id="rconForm" onsubmit="return executeCommand(event);">
                <div class="input-group">
                    <input type="text" 
                           id="commandInput" 
                           class="console-input" 
                           placeholder="Enter RCON command..." 
                           autocomplete="off">
                    <button type="submit" class="btn btn-primary">
                        Execute
                    </button>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-info">
                ℹ️ You can only use the quick command buttons above. Custom commands are restricted.
            </div>
            <?php endif; ?>
        </div>

        <div class="command-help">
            <h3>📚 Common Commands:</h3>
            <div class="help-grid">
                <div class="help-item">
                    <code>SaveWorld</code>
                    <span>Save the current world state</span>
                </div>
                <div class="help-item">
                    <code>ListPlayers</code>
                    <span>Show connected players</span>
                </div>
                <div class="help-item">
                    <code>Broadcast &lt;message&gt;</code>
                    <span>Send message to all players</span>
                </div>
                <div class="help-item">
                    <code>ServerChat &lt;message&gt;</code>
                    <span>Send chat message as server</span>
                </div>
                <div class="help-item">
                    <code>DestroyWildDinos</code>
                    <span>Destroy all wild dinosaurs (respawn)</span>
                </div>
                <div class="help-item">
                    <code>SetTimeOfDay HH:MM:SS</code>
                    <span>Set time of day (e.g., 12:00:00)</span>
                </div>
                <div class="help-item">
                    <code>KickPlayer &lt;SteamID&gt;</code>
                    <span>Kick a player from server</span>
                </div>
                <div class="help-item">
                    <code>BanPlayer &lt;SteamID&gt;</code>
                    <span>Ban a player from server</span>
                </div>
                <div class="help-item">
                    <code>DoExit</code>
                    <span>Shutdown the server gracefully</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const serverKey = '<?php echo addslashes($selectedServer); ?>';
const commandHistory = [];
let historyIndex = -1;

function addConsoleOutput(text, type = 'response') {
    const output = document.getElementById('consoleOutput');
    const line = document.createElement('div');
    line.className = 'console-line console-' + type;
    
    const timestamp = new Date().toLocaleTimeString();
    line.innerHTML = `<span class="timestamp">[${timestamp}]</span> ${escapeHtml(text)}`;
    
    output.appendChild(line);
    output.scrollTop = output.scrollHeight;
}

async function executeQuickCommand(command, isDangerous) {
    if (isDangerous && !confirm('Execute: ' + command + '?\n\nThis is a potentially dangerous command.')) {
        return;
    }
    
    // Show command in console
    addConsoleOutput('> ' + command, 'command');
    
    // Execute immediately
    try {
        const formData = new FormData();
        formData.append('action', 'execute');
        formData.append('server', serverKey);
        formData.append('command', command);
        
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            const responseText = result.response || '(No response)';
            addConsoleOutput(responseText, 'response');
        } else {
            addConsoleOutput('Error: ' + result.error, 'error');
        }
    } catch (error) {
        addConsoleOutput('Request failed: ' + error.message, 'error');
    }
}

async function executeCommand(event) {
    event.preventDefault();
    
    const input = document.getElementById('commandInput');
    const command = input.value.trim();
    
    if (!command) return false;
    
    // Add to history
    commandHistory.unshift(command);
    historyIndex = -1;
    
    // Show command in console
    addConsoleOutput('> ' + command, 'command');
    
    // Clear input
    input.value = '';
    
    // Execute via AJAX
    try {
        const formData = new FormData();
        formData.append('action', 'execute');
        formData.append('server', serverKey);
        formData.append('command', command);
        
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            const responseText = result.response || '(No response)';
            addConsoleOutput(responseText, 'response');
        } else {
            addConsoleOutput('Error: ' + result.error, 'error');
        }
    } catch (error) {
        addConsoleOutput('Request failed: ' + error.message, 'error');
    }
    
    return false;
}

<?php if (hasPermission('run_custom_commands') || hasPermission('all')): ?>
// Command history navigation
document.getElementById('commandInput').addEventListener('keydown', function(e) {
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (historyIndex < commandHistory.length - 1) {
            historyIndex++;
            this.value = commandHistory[historyIndex];
        }
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (historyIndex > 0) {
            historyIndex--;
            this.value = commandHistory[historyIndex];
        } else if (historyIndex === 0) {
            historyIndex = -1;
            this.value = '';
        }
    }
});
<?php endif; ?>

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include '../includes/footer.php'; ?>