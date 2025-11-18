<?php
/**
 * Log Viewer - Real-time Server Logs
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!hasPermission('view_logs')) {
    die('Access denied.');
}

// AJAX endpoint for fetching log updates
if (isset($_GET['ajax']) && $_GET['ajax'] === 'fetch') {
    $logType = $_GET['type'] ?? 'server';
    $lastSize = (int)($_GET['size'] ?? 0);
    
    $logPath = match($logType) {
        'manager' => __DIR__ . '/../logs/manager.log',
        'server' => ARK_LOGS_DIR . '\\ShooterGame.log',
        default => null
    };
    
    if (!$logPath || !file_exists($logPath)) {
        jsonResponse(['success' => false, 'error' => 'Log file not found']);
    }
    
    $currentSize = filesize($logPath);
    
    if ($currentSize <= $lastSize) {
        jsonResponse(['success' => true, 'new_content' => '', 'size' => $currentSize]);
    }
    
    $handle = fopen($logPath, 'r');
    fseek($handle, $lastSize);
    $newContent = fread($handle, $currentSize - $lastSize);
    fclose($handle);
    
    jsonResponse([
        'success' => true,
        'new_content' => $newContent,
        'size' => $currentSize
    ]);
}

$selectedLog = $_GET['log'] ?? 'server';
$lines = (int)($_GET['lines'] ?? 100);
$lines = max(10, min(1000, $lines)); // Between 10 and 1000

$availableLogs = [
    'server' => [
        'name' => 'ARK Server Log',
        'path' => ARK_LOGS_DIR . '\\ShooterGame.log'
    ],
    'manager' => [
        'name' => 'Manager Actions Log',
        'path' => __DIR__ . '/../logs/manager.log'
    ]
];

if (!isset($availableLogs[$selectedLog])) {
    $selectedLog = 'server';
}

$logInfo = $availableLogs[$selectedLog];
$logPath = $logInfo['path'];
$logExists = file_exists($logPath);

$logContent = [];
$fileSize = 0;

if ($logExists) {
    $logContent = tailFile($logPath, $lines);
    $fileSize = filesize($logPath);
}

$pageTitle = 'Log Viewer';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>📋 Log Viewer</h1>
    <p>Real-time server and manager logs</p>
</div>

<div class="logs-container">
    <div class="log-controls">
        <div class="log-selector">
            <label>Select Log:</label>
            <select id="logSelect" class="form-control" onchange="changeLog(this.value)">
                <?php foreach ($availableLogs as $key => $log): ?>
                    <option value="<?php echo $key; ?>" <?php echo $selectedLog === $key ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($log['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="line-selector">
            <label>Lines:</label>
            <select id="linesSelect" class="form-control" onchange="changeLines(this.value)">
                <option value="50" <?php echo $lines === 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo $lines === 100 ? 'selected' : ''; ?>>100</option>
                <option value="250" <?php echo $lines === 250 ? 'selected' : ''; ?>>250</option>
                <option value="500" <?php echo $lines === 500 ? 'selected' : ''; ?>>500</option>
                <option value="1000" <?php echo $lines === 1000 ? 'selected' : ''; ?>>1000</option>
            </select>
        </div>

        <div class="log-actions">
            <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" class="btn btn-primary">
                ⏸️ Pause Auto-Refresh
            </button>
            <label style="color: white; margin-left: 1rem;">
                <input type="checkbox" id="autoScrollCheckbox" checked onchange="toggleAutoScroll()">
                Auto-scroll to bottom
            </label>
            <button onclick="clearLog()" class="btn btn-secondary">
                🗑️ Clear Display
            </button>
            <button onclick="downloadLog()" class="btn btn-info">
                📥 Download Full Log
            </button>
        </div>
    </div>

    <div class="log-info-bar">
        <span><strong>File:</strong> <?php echo htmlspecialchars($logPath); ?></span>
        <?php if ($logExists): ?>
            <span><strong>Size:</strong> <?php echo formatFileSize($fileSize); ?></span>
            <span id="lastUpdate"><strong>Last Update:</strong> Just now</span>
        <?php else: ?>
            <span class="text-error"><strong>Status:</strong> File not found</span>
        <?php endif; ?>
        <span id="autoRefreshStatus" class="text-success"><strong>Auto-Refresh:</strong> ✅ Enabled (5s)</span>
    </div>

    <?php if (!$logExists): ?>
        <div class="alert alert-warning">
            ⚠️ Log file not found. The server may not have created logs yet, or the path is incorrect.
        </div>
    <?php endif; ?>

    <div class="log-viewer-container">
        <div id="logContent" class="log-content">
            <?php if ($logExists && !empty($logContent)): ?>
                <?php foreach ($logContent as $line): ?>
                    <div class="log-line"><?php echo htmlspecialchars($line); ?></div>
                <?php endforeach; ?>
            <?php elseif ($logExists): ?>
                <div class="log-line text-muted">(Log file is empty)</div>
            <?php else: ?>
                <div class="log-line text-muted">(No log file available)</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="log-search">
        <input type="text" 
               id="searchInput" 
               class="form-control" 
               placeholder="Search logs (press Enter)..."
               onkeyup="searchLogs(event)">
    </div>
</div>

<script>
let autoRefreshEnabled = true;
let autoRefreshInterval = null;
let currentFileSize = <?php echo $fileSize; ?>;
let autoScrollEnabled = true;
const currentLogType = '<?php echo addslashes($selectedLog); ?>';
const currentLines = <?php echo $lines; ?>;

function changeLog(logType) {
    window.location = `?log=${logType}&lines=${currentLines}`;
}

function changeLines(lines) {
    window.location = `?log=${currentLogType}&lines=${lines}`;
}

function toggleAutoScroll() {
    autoScrollEnabled = document.getElementById('autoScrollCheckbox').checked;
}

function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;
    
    const btn = document.getElementById('autoRefreshBtn');
    const status = document.getElementById('autoRefreshStatus');
    
    if (autoRefreshEnabled) {
        btn.textContent = '⏸️ Pause Auto-Refresh';
        btn.className = 'btn btn-primary';
        status.innerHTML = '<strong>Auto-Refresh:</strong> ✅ Enabled (5s)';
        status.className = 'text-success';
        startAutoRefresh();
    } else {
        btn.textContent = '▶️ Resume Auto-Refresh';
        btn.className = 'btn btn-secondary';
        status.innerHTML = '<strong>Auto-Refresh:</strong> ⏸️ Paused';
        status.className = 'text-muted';
        stopAutoRefresh();
    }
}

function startAutoRefresh() {
    if (autoRefreshInterval) return;
    
    autoRefreshInterval = setInterval(fetchNewLogs, 5000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

async function fetchNewLogs() {
    if (!autoRefreshEnabled) return;
    
    try {
        const response = await fetch(`?ajax=fetch&type=${currentLogType}&size=${currentFileSize}`);
        const result = await response.json();
        
        if (result.success && result.new_content) {
            const logContent = document.getElementById('logContent');
            const lines = result.new_content.split('\n');
            
            lines.forEach(line => {
                if (line.trim()) {
                    const lineDiv = document.createElement('div');
                    lineDiv.className = 'log-line';
                    lineDiv.textContent = line;
                    logContent.appendChild(lineDiv);
                }
            });
            
            // Auto-scroll to bottom if enabled
            if (autoScrollEnabled) {
                logContent.scrollTop = logContent.scrollHeight;
            }
            
            currentFileSize = result.size;
            
            // Update last update time
            document.getElementById('lastUpdate').innerHTML = 
                '<strong>Last Update:</strong> ' + new Date().toLocaleTimeString();
        }
    } catch (error) {
        console.error('Failed to fetch logs:', error);
    }
}

function clearLog() {
    if (confirm('Clear the log display? This will not delete the actual log file.')) {
        document.getElementById('logContent').innerHTML = 
            '<div class="log-line text-muted">(Display cleared)</div>';
    }
}

function downloadLog() {
    window.location = 'download.php?log=<?php echo urlencode($selectedLog); ?>';
}

function searchLogs(event) {
    if (event.key !== 'Enter') return;
    
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const lines = document.querySelectorAll('.log-line');
    
    if (!searchTerm) {
        lines.forEach(line => {
            line.style.display = '';
            line.classList.remove('highlighted');
        });
        return;
    }
    
    lines.forEach(line => {
        const text = line.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            line.style.display = '';
            line.classList.add('highlighted');
        } else {
            line.style.display = 'none';
            line.classList.remove('highlighted');
        }
    });
}

// Start auto-refresh on load
if (autoRefreshEnabled) {
    startAutoRefresh();
}

// Scroll to bottom on initial load if checkbox is checked
if (autoScrollEnabled) {
    const logContent = document.getElementById('logContent');
    if (logContent) {
        logContent.scrollTop = logContent.scrollHeight;
    }
}

// Stop auto-refresh when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
    } else if (autoRefreshEnabled) {
        startAutoRefresh();
    }
});
</script>

<?php include '../includes/footer.php'; ?>