<?php

require_once "secure/safe_exec.php";

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check permission
if (!hasPermission('view_scripts')) {
    die('Access denied');
}

// Handle script execution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'run_script') {
        $scriptKey = $_POST['script'] ?? '';
		$scriptPath = $BATCH_FILES[$scriptKey];
        
        if (!isset($BATCH_FILES[$scriptKey])) {
			$message = '❌ Invalid script key - ' . $scriptKey;
			logAction(__FILE__, __LINE__, 'SCRIPT_RUN', 'Results: ' . $message);
		} elseif (!file_exists($scriptPath)) {
			$message = '❌ Script file not found - ' . $scriptPath;
			logAction(__FILE__, __LINE__, 'SCRIPT_RUN', 'Results: ' . $message);
		} else {
			$message = '✅ Script execution started: ' . htmlspecialchars(basename($scriptPath));
			
		  //Execute the batch file
		  //$command = 'start "" "' . $scriptPath . '"';
		  //pclose(popen($command, 'r'));
		    logAction(__FILE__, __LINE__, 'SCRIPT_RUN', $scriptPath);
		    logAction(__FILE__, __LINE__, 'SCRIPT_RUN', 'Results: ' . $message);
		}
                		
    }
    
    if ($_POST['action'] === 'run_custom') {
        $command = $_POST['command'] ?? '';
        
        if (empty($command)) {
            jsonResponse(['success' => false, 'error' => 'Command cannot be empty']);
        }
        
        // Execute command
        $output = [];
        $returnCode = 0;
        //exec($command . ' 2>&1', $output, $returnCode);
		safe_exec($command . ' 2>&1', $output, $returnCode);
        
        logAction(__FILE__, __LINE__, 'CUSTOM_COMMAND', $command);
        
        jsonResponse([
            'success' => $returnCode === 0,
            'output' => implode("\n", $output),
            'return_code' => $returnCode
        ]);
    }
}

$pageTitle = 'Scripts & Commands';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>⚙️ Scripts & Commands</h1>
    <p>Execute batch files, Python scripts, and custom commands</p>
</div>

<div class="scripts-container">
    <!-- Batch Files Section -->
    <div class="scripts-section">
        <h2>📜 Server Batch Files</h2>
        <div class="scripts-grid">
            <?php foreach ($BATCH_FILES as $key => $path): ?>
                <?php
                $exists = file_exists($path);
                $fileInfo = $exists ? [
                    'size' => filesize($path),
                    'modified' => filemtime($path)
                ] : null;
                ?>
                <div class="script-card <?php echo $exists ? '' : 'script-missing'; ?>">
                    <div class="script-header">
                        <h3><?php echo htmlspecialchars(ucfirst($key)); ?></h3>
                        <span class="script-status">
                            <?php echo $exists ? '✅' : '❌'; ?>
                        </span>
                    </div>
                    <div class="script-body">
                        <p class="script-path"><?php echo htmlspecialchars($path); ?></p>
                        <?php if ($fileInfo): ?>
                            <p class="script-info">
                                Size: <?php echo formatFileSize($fileInfo['size']); ?> | 
                                Modified: <?php echo date('Y-m-d H:i', $fileInfo['modified']); ?>
                            </p>
                        <?php else: ?>
                            <p class="text-error">File not found</p>
                        <?php endif; ?>
                    </div>
                    <div class="script-footer">
                        <?php if ($exists): ?>
                            <button onclick="runScript('<?php echo htmlspecialchars($key); ?>')" 
                                    class="btn btn-success">
                                ▶️ Run Script
                            </button>
                            <a href="view.php?script=<?php echo urlencode($key); ?>" 
                               class="btn btn-secondary">
                                👁️ View
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Custom Command Section -->
    <div class="custom-command-section">
        <h2>💻 Custom Command Executor</h2>
        <div class="command-panel">
            <form id="customCommandForm" onsubmit="return runCustomCommand(event);">
                <div class="form-group">
                    <label>Enter Command:</label>
                    <input type="text" 
                           id="customCommand" 
                           class="form-control" 
                           placeholder="e.g., tasklist, dir, ipconfig"
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary">▶️ Execute</button>
            </form>
            
            <div id="commandOutput" class="command-output" style="display: none;">
                <h4>Output:</h4>
                <pre id="outputContent"></pre>
            </div>
        </div>
        
        <div class="command-examples">
            <h4>Quick Commands:</h4>
            <?php
            global $SCRIPT_QUICK_COMMANDS;
            foreach ($SCRIPT_QUICK_COMMANDS as $label => $command):
            ?>
                <button onclick="setCommand('<?php echo htmlspecialchars($command, ENT_QUOTES); ?>')" 
                        class="btn btn-sm btn-secondary">
                    <?php echo htmlspecialchars($label); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Actions Log -->
    <div class="recent-actions-section">
        <h2>📋 Recent Script Executions</h2>
        <div class="log-viewer">
            <?php
            $logFile = __DIR__ . '/../logs/manager.log';
            if (file_exists($logFile)) {
                $logs = tailFile($logFile, 20);
                $scriptLogs = array_filter($logs, function($line) {
                    return strpos($line, 'CUSTOM_COMMAND') !== false || 
                           strpos($line, 'SCRIPT_RUN') !== false;
                });
                
                if (!empty($scriptLogs)):
                    foreach (array_slice($scriptLogs, -10) as $log):
            ?>
                    <div class="result log-entry"><?php echo ($log); ?></div>
                    <!-- <div class="result log-entry"><?php echo htmlspecialchars($log); ?></div> -->
            <?php
                    endforeach;
                else:
            ?>
                    <p class="text-muted">No recent script executions</p>
            <?php
                endif;
            } else {
            ?>
                <p class="text-muted">Log file not found</p>
            <?php
            }
            ?>
        </div>
    </div>

    <!-- Safety Warning -->
    <div class="alert alert-warning">
        <strong>⚠️ Warning:</strong> Be careful when executing custom commands. 
        Commands run with the same permissions as the web server (LocalSystem).
        Always verify commands before execution.
    </div>
</div>

<script>
async function runScript(scriptKey) {
    if (!confirm(`Run ${scriptKey} batch file?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'run_script');
    formData.append('script', scriptKey);
    
    try {
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ ' + result.message + '\n\nScript: ' + result.script);
        } else {
            alert('❌ Error: ' + result.error);
        }
    } catch (error) {
        alert('❌ Request failed: ' + error.message);
    }
}

async function runCustomCommand(event) {
    event.preventDefault();
    
    const command = document.getElementById('customCommand').value.trim();
    
    if (!command) {
        alert('Please enter a command');
        return false;
    }
    
    const outputDiv = document.getElementById('commandOutput');
    const outputContent = document.getElementById('outputContent');
    
    outputDiv.style.display = 'block';
    outputContent.textContent = 'Executing...';
    
    const formData = new FormData();
    formData.append('action', 'run_custom');
    formData.append('command', command);
    
    try {
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            outputContent.textContent = result.output || '(No output)';
        } else {
            outputContent.textContent = `Error (Code ${result.return_code}):\n${result.output}`;
        }
    } catch (error) {
        outputContent.textContent = 'Request failed: ' + error.message;
    }
    
    return false;
}

function setCommand(cmd) {
    document.getElementById('customCommand').value = cmd;
    document.getElementById('customCommand').focus();
}
</script>

<?php include '../includes/footer.php'; ?>