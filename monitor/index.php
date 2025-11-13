<?php
/**
 * System Monitor - CPU, RAM, Network, Temps
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check permission
if (!hasPermission('view_system_monitor')) {
    die('Access denied');
}

// AJAX endpoint for live data
if (isset($_GET['ajax']) && $_GET['ajax'] === 'stats') {
    header('Content-Type: application/json');
    
    $stats = [];
    
    // Get CPU usage
    $cpuOutput = [];
    exec('wmic cpu get loadpercentage /format:value 2>&1', $cpuOutput);
    foreach ($cpuOutput as $line) {
        if (strpos($line, 'LoadPercentage=') !== false) {
            $stats['cpu'] = (int)str_replace('LoadPercentage=', '', trim($line));
            break;
        }
    }
    
    // Get Memory usage
    $memOutput = [];
    exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /format:value 2>&1', $memOutput);
    $total = 0;
    $free = 0;
    foreach ($memOutput as $line) {
        if (strpos($line, 'TotalVisibleMemorySize=') !== false) {
            $total = (int)str_replace('TotalVisibleMemorySize=', '', trim($line));
        }
        if (strpos($line, 'FreePhysicalMemory=') !== false) {
            $free = (int)str_replace('FreePhysicalMemory=', '', trim($line));
        }
    }
    if ($total > 0) {
        $used = $total - $free;
        $stats['memory_percent'] = round(($used / $total) * 100, 1);
        $stats['memory_used_gb'] = round($used / 1024 / 1024, 2);
        $stats['memory_total_gb'] = round($total / 1024 / 1024, 2);
    }
    
    // Get Disk usage
    $diskOutput = [];
    exec('wmic logicaldisk where "DriveType=3" get FreeSpace,Size,DeviceID /format:csv 2>&1', $diskOutput);
    $stats['disks'] = [];
    foreach ($diskOutput as $line) {
        if (empty(trim($line)) || strpos($line, 'DeviceID') !== false || strpos($line, 'Node') !== false) continue;
        $parts = str_getcsv($line);
        if (count($parts) >= 4) {
            $drive = $parts[1];
            $free = (int)$parts[2];
            $total = (int)$parts[3];
            if ($total > 0) {
                $stats['disks'][] = [
                    'drive' => $drive,
                    'used_percent' => round((($total - $free) / $total) * 100, 1),
                    'free_gb' => round($free / 1024 / 1024 / 1024, 2),
                    'total_gb' => round($total / 1024 / 1024 / 1024, 2),
                ];
            }
        }
    }
    
    // Get Network stats
    $netOutput = [];
    exec('netstat -e 2>&1', $netOutput);
    foreach ($netOutput as $line) {
        if (preg_match('/Bytes\s+(\d+)\s+(\d+)/', $line, $matches)) {
            $stats['network_received_mb'] = round($matches[1] / 1024 / 1024, 2);
            $stats['network_sent_mb'] = round($matches[2] / 1024 / 1024, 2);
        }
    }
    
    // Get ARK Server process stats
    $arkStats = [];
    $arkOutput = [];
    exec('wmic process where "name=\'ShooterGameServer.exe\'" get ProcessId,WorkingSetSize,PercentProcessorTime /format:csv 2>&1', $arkOutput);
    foreach ($arkOutput as $line) {
        if (empty(trim($line)) || strpos($line, 'Node') !== false || strpos($line, 'PercentProcessorTime') !== false) continue;
        $parts = str_getcsv($line);
        if (count($parts) >= 4) {
            $arkStats[] = [
                'pid' => $parts[2],
                'memory_mb' => round($parts[3] / 1024 / 1024, 2),
            ];
        }
    }
    $stats['ark_servers'] = $arkStats;
    
    // System uptime
    $uptimeOutput = [];
    exec('wmic os get lastbootuptime /format:value 2>&1', $uptimeOutput);
    foreach ($uptimeOutput as $line) {
        if (strpos($line, 'LastBootUpTime=') !== false) {
            $bootTime = str_replace('LastBootUpTime=', '', trim($line));
            // Parse: 20250115141530.500000-300
            if (preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $bootTime, $matches)) {
                $bootTimestamp = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
                $uptime = time() - $bootTimestamp;
                $days = floor($uptime / 86400);
                $hours = floor(($uptime % 86400) / 3600);
                $minutes = floor(($uptime % 3600) / 60);
                $stats['uptime'] = "{$days}d {$hours}h {$minutes}m";
            }
        }
    }
    
    echo json_encode($stats);
    exit;
}

$pageTitle = 'System Monitor';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>📊 System Monitor</h1>
    <p>Real-time system performance and resource usage</p>
</div>

<div class="monitor-container">
    <div class="monitor-controls">
        <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" class="btn btn-primary">
            ⏸️ Pause Refresh
        </button>
        <span id="lastUpdate" style="color: white; margin-left: 1rem;">
            Last Update: <span id="updateTime">--:--:--</span>
        </span>
    </div>

    <div class="stats-grid" style="margin-top: 2rem;">
        <!-- CPU Card -->
        <div class="stat-card">
            <h3>💻 CPU Usage</h3>
            <div class="metric-value">
                <span id="cpuValue" style="font-size: 3rem; font-weight: bold; color: var(--primary-color);">--%</span>
            </div>
            <div class="progress-bar">
                <div id="cpuProgress" class="progress-fill" style="width: 0%;"></div>
            </div>
        </div>

        <!-- Memory Card -->
        <div class="stat-card">
            <h3>🧠 Memory Usage</h3>
            <div class="metric-value">
                <span id="memValue" style="font-size: 3rem; font-weight: bold; color: var(--primary-color);">--%</span>
            </div>
            <div class="metric-details">
                <span id="memDetails">-- GB / -- GB</span>
            </div>
            <div class="progress-bar">
                <div id="memProgress" class="progress-fill" style="width: 0%;"></div>
            </div>
        </div>

        <!-- Network Card -->
        <div class="stat-card">
            <h3>🌐 Network Usage</h3>
            <div class="metric-details" style="margin-top: 1rem;">
                <p>📥 Received: <span id="netReceived">-- MB</span></p>
                <p>📤 Sent: <span id="netSent">-- MB</span></p>
            </div>
        </div>

        <!-- Uptime Card -->
        <div class="stat-card">
            <h3>⏰ System Uptime</h3>
            <div class="metric-value" style="margin-top: 1rem;">
                <span id="uptimeValue" style="font-size: 2rem; color: var(--primary-color);">--d --h --m</span>
            </div>
        </div>
    </div>

    <!-- Disk Usage Section -->
    <div class="monitor-section">
        <h2>💾 Disk Usage</h2>
        <div id="diskStats" class="disk-grid"></div>
    </div>

    <!-- ARK Server Processes -->
    <div class="monitor-section">
        <h2>🦖 ARK Server Processes</h2>
        <div id="arkStats" class="ark-processes"></div>
    </div>
</div>

<style>
.monitor-container {
    max-width: 1400px;
    margin: 0 auto;
}

.monitor-controls {
    background: var(--card-bg);
    padding: 1rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.metric-value {
    text-align: center;
    margin: 1rem 0;
}

.metric-details {
    text-align: center;
    color: var(--text-secondary);
    margin-top: 0.5rem;
}

.progress-bar {
    width: 100%;
    height: 30px;
    background: var(--darker-bg);
    border-radius: 15px;
    overflow: hidden;
    margin-top: 1rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    transition: width 0.5s ease;
}

.monitor-section {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    margin-top: 2rem;
    border: 1px solid var(--border-color);
}

.monitor-section h2 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.disk-grid {
    display: grid;
    gap: 1rem;
}

.disk-item {
    background: var(--darker-bg);
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.ark-processes {
    display: grid;
    gap: 1rem;
}

.ark-process-item {
    background: var(--darker-bg);
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>

<script>
let autoRefreshEnabled = true;
let autoRefreshInterval = null;

async function fetchStats() {
    try {
        const response = await fetch('?ajax=stats');
        const stats = await response.json();
        
        // Update CPU
        if (stats.cpu !== undefined) {
            document.getElementById('cpuValue').textContent = stats.cpu + '%';
            document.getElementById('cpuProgress').style.width = stats.cpu + '%';
        }
        
        // Update Memory
        if (stats.memory_percent !== undefined) {
            document.getElementById('memValue').textContent = stats.memory_percent + '%';
            document.getElementById('memProgress').style.width = stats.memory_percent + '%';
            document.getElementById('memDetails').textContent = 
                `${stats.memory_used_gb} GB / ${stats.memory_total_gb} GB`;
        }
        
        // Update Network
        if (stats.network_received_mb !== undefined) {
            document.getElementById('netReceived').textContent = stats.network_received_mb + ' MB';
            document.getElementById('netSent').textContent = stats.network_sent_mb + ' MB';
        }
        
        // Update Uptime
        if (stats.uptime) {
            document.getElementById('uptimeValue').textContent = stats.uptime;
        }
        
        // Update Disks
        if (stats.disks && stats.disks.length > 0) {
            const diskHtml = stats.disks.map(disk => `
                <div class="disk-item">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <strong>${disk.drive}</strong>
                        <span>${disk.used_percent}% used</span>
                    </div>
                    <div class="progress-bar" style="height: 20px;">
                        <div class="progress-fill" style="width: ${disk.used_percent}%;"></div>
                    </div>
                    <div style="margin-top: 0.5rem; color: var(--text-secondary); font-size: 0.9rem;">
                        ${disk.free_gb} GB free of ${disk.total_gb} GB
                    </div>
                </div>
            `).join('');
            document.getElementById('diskStats').innerHTML = diskHtml;
        }
        
        // Update ARK Servers
        if (stats.ark_servers && stats.ark_servers.length > 0) {
            const arkHtml = stats.ark_servers.map(server => `
                <div class="ark-process-item">
                    <div>
                        <strong>🦖 ARK Server</strong><br>
                        <span style="color: var(--text-secondary);">PID: ${server.pid}</span>
                    </div>
                    <div style="text-align: right;">
                        <strong style="color: var(--primary-color);">${server.memory_mb} MB</strong><br>
                        <span style="color: var(--text-secondary);">Memory</span>
                    </div>
                </div>
            `).join('');
            document.getElementById('arkStats').innerHTML = arkHtml;
        } else {
            document.getElementById('arkStats').innerHTML = 
                '<p style="color: var(--text-secondary);">No ARK servers currently running</p>';
        }
        
        // Update timestamp
        document.getElementById('updateTime').textContent = new Date().toLocaleTimeString();
        
    } catch (error) {
        console.error('Failed to fetch stats:', error);
    }
}

function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;
    
    const btn = document.getElementById('autoRefreshBtn');
    
    if (autoRefreshEnabled) {
        btn.textContent = '⏸️ Pause Refresh';
        btn.className = 'btn btn-primary';
        startAutoRefresh();
    } else {
        btn.textContent = '▶️ Resume Refresh';
        btn.className = 'btn btn-secondary';
        stopAutoRefresh();
    }
}

function startAutoRefresh() {
    if (autoRefreshInterval) return;
    
    fetchStats(); // Immediate fetch
    autoRefreshInterval = setInterval(fetchStats, 3000); // Every 3 seconds
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

// Start on page load
startAutoRefresh();

// Stop when page hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
    } else if (autoRefreshEnabled) {
        startAutoRefresh();
    }
});
</script>

<?php include '../includes/footer.php'; ?>