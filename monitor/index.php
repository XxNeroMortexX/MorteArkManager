<?php

require_once "secure/safe_exec.php";

/**
 * System Monitor - Task Manager Style with LHM Integration
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check permission
if (!hasPermission('view_system_monitor')) {
    die('Access denied');
}

// LibreHardwareMonitor settings
define('LHM_URL', 'http://192.168.1.3:8085/data.json');

// Scheduled restart settings
define('TASK_NAME', 'Auto ShutDown'); // Name of your scheduled task

// Function to get schedule from Task Scheduler
function getScheduledRestartInfo() {
    $taskName = TASK_NAME;
    
    // Get task info using PowerShell
    $command = "powershell -Command \"Get-ScheduledTask -TaskName '" . $taskName . "' | Get-ScheduledTaskInfo; Get-ScheduledTask -TaskName '" . $taskName . "' | Select-Object -ExpandProperty Triggers\"";
    
    $output = [];
    //exec($command . ' 2>&1', $output);
	safe_exec($command . ' 2>&1', $output);
    
    $schedule = [
        'days' => [],
        'shutdown_time' => '',
        'startup_time' => '8:30 AM', // Can't be read from BIOS RTC
        'recurrence' => ''
    ];
    
    // Parse output for trigger information
    $foundDays = false;
    foreach ($output as $line) {
        // Look for DaysOfWeek
        if (preg_match('/DaysOfWeek\s*:\s*(.+)/', $line, $matches)) {
            $daysStr = trim($matches[1]);
            // Parse days: "Sunday, Wednesday" or "11" (bitfield)
            if (is_numeric($daysStr)) {
                // Convert bitfield to day names
                $dayMap = [1 => 'Sunday', 2 => 'Monday', 4 => 'Tuesday', 8 => 'Wednesday', 16 => 'Thursday', 32 => 'Friday', 64 => 'Saturday'];
                $days = [];
                foreach ($dayMap as $bit => $day) {
                    if ((int)$daysStr & $bit) {
                        $days[] = $day;
                    }
                }
                $schedule['days'] = $days;
            } else {
                $schedule['days'] = array_map('trim', explode(',', $daysStr));
            }
            $foundDays = true;
        }
        
        // Look for StartBoundary (contains time)
        if (preg_match('/StartBoundary\s*:\s*(.+)/', $line, $matches)) {
            $dateTimeStr = trim($matches[1]);
            // Format: 2020-11-23T06:30:00
            if (preg_match('/T(\d{2}):(\d{2})/', $dateTimeStr, $timeMatch)) {
                $hour = (int)$timeMatch[1];
                $minute = $timeMatch[2];
                $ampm = $hour >= 12 ? 'PM' : 'AM';
                $hour12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
                $schedule['shutdown_time'] = "$hour12:$minute $ampm";
            }
        }
        
        // Look for recurrence pattern
        if (preg_match('/Weekly/', $line)) {
            $schedule['recurrence'] = 'Weekly';
        } elseif (preg_match('/Daily/', $line)) {
            $schedule['recurrence'] = 'Daily';
        }
    }
    
    // Set defaults if not found
    if (empty($schedule['days'])) {
        $schedule['days'] = ['Sunday', 'Wednesday'];
    }
    if (empty($schedule['shutdown_time'])) {
        $schedule['shutdown_time'] = '6:30 AM';
    }
    if (empty($schedule['recurrence'])) {
        $schedule['recurrence'] = 'Weekly';
    }
    
    return $schedule;
}

/**
 * Converts a .NET-style JSON date string into a Unix timestamp.
 *
 * .NET JSON dates are typically in the format: "/Date(1763305500421)/"
 * where the number represents milliseconds since the Unix epoch (Jan 1, 1970).
 *
 * This function extracts the millisecond value, converts it to seconds,
 * and returns it as a standard Unix timestamp for use in PHP.
 *
 * @param string $dotNetDate The .NET JSON date string (e.g., "/Date(1763305500421)/")
 * @return int|null Unix timestamp in seconds, or null if the input is invalid
 */
function parseDotNetDate($dotNetDate) {
    if (preg_match('/\/Date\((\d+)\)\//', $dotNetDate, $matches)) {
        $ms = $matches[1];
        return (int)($ms / 1000); // convert milliseconds to seconds
    }
    return null;
}

// AJAX endpoint for live data
if (isset($_GET['ajax']) && $_GET['ajax'] === 'stats') {
	
	// To Debug add "?ajax=stats" at end of URL.
    header('Content-Type: application/json');
    
    $stats = [];
        		
	// === DISK USAGE ===
	$stats['disks'] = [];
	foreach (range('A', 'Z') as $driveLetter) {
		$drivePath = $driveLetter . ':\\';
		if (is_dir($drivePath)) {
			$total = @disk_total_space($drivePath);
			$free  = @disk_free_space($drivePath);

			if ($total && $free !== false) {
				$used = $total - $free;
				$stats['disks'][] = [
					'drive'        => $driveLetter . ':',
					'used_percent' => round(($used / $total) * 100, 1),
					'used_gb'      => round($used / 1024 / 1024 / 1024, 2),
					'free_gb'      => round($free / 1024 / 1024 / 1024, 2),
					'total_gb'     => round($total / 1024 / 1024 / 1024, 2),
				];
			}
		}
	}
    
    // === NETWORK SPEED (Current transfer rate) ===
    $netOutput = [];
    //exec('netstat -e 2>&1', $netOutput);
	safe_exec('netstat -e 2>&1', $netOutput);
    
    $bytesReceived = 0;
    $bytesSent = 0;
    
    foreach ($netOutput as $line) {
        // Parse: "Bytes                   123456789       987654321"
        if (preg_match('/Bytes\s+(\d+)\s+(\d+)/', $line, $matches)) {
            $bytesReceived = (float)$matches[1];
            $bytesSent = (float)$matches[2];
            break;
        }
    }
    
    $stats['network_received_bytes'] = $bytesReceived;
    $stats['network_sent_bytes'] = $bytesSent;
    
    // === LIBRE HARDWARE MONITOR DATA ===
    $lhmData = @file_get_contents(LHM_URL);
    if ($lhmData) {
        $lhm = json_decode($lhmData, true);
        
        // Parse LHM data recursively
        $temps = [];
        $fans = [];
        $gpuUsage = 0;
        $cpuUsage = 0;
        $gpuMemUsed = 0;
        $gpuMemTotal = 0;
		$memoryUsed = 0;
		$memoryFree = 0;
        
        function parseLHM($node, &$temps, &$fans, &$gpuUsage, &$gpuMemUsed, &$gpuMemTotal, &$cpuUsage, &$memoryUsed, &$memoryFree, $parentName = '') {
            if (isset($node['Text']) && isset($node['Value'])) {
				$text = $node['Text'] ?? null;
				$valueStr = $node['Value'] ?? null;
                
				// Recurse through children anyway
				if (isset($node['Children'])) {
					foreach ($node['Children'] as $child) {
						parseLHM($child, $temps, $fans, $gpuUsage, $gpuMemUsed, $gpuMemTotal, $cpuUsage, $memoryUsed, $memoryFree, $text);
					}
				}
                
                // Clean up value
                $value = (float)str_replace([' °C', ' RPM', ' MHz', ' %', ' GB', ' MB'], '', $valueStr);
                
				// === CPU USAGE ===
				if ($text === "CPU Total" && stripos($valueStr, '%') !== false) {
					$cpuUsage = $value;
				}
				
				// === MEMORY USAGE ===
				if ($text === "Memory Used" && stripos($valueStr, 'GB') !== false) {
					$memoryUsed = floatval($valueStr);
				}
				if ($text === "Memory Available" && stripos($valueStr, 'GB') !== false) {
					$memoryFree = $value;
				}

                // === GPU USAGE ===
                if ($text === "GPU Core" && stripos($valueStr, '%') !== false) {
                    $gpuUsage = $value;
                }
                
                // === GPU MEMORY ===
                if ($text === "GPU Memory Used" && stripos($valueStr, 'MB') !== false) {
                    $gpuMemUsed = $value;
                }
                if ($text === "GPU Memory Total" && stripos($valueStr, 'MB') !== false) {
                    $gpuMemTotal = $value;
                }
				               
                // === TEMPERATURES ===
                if (stripos($valueStr, '°C') !== false && $value > 0) {
                    // CPU - Look for "Core (Tctl/Tdie)"
                    if (stripos($text, 'Tctl') !== false || stripos($text, 'Tdie') !== false) {
                        $temps['CPU Package'] = $value;
                    }
                    // GPU - "GPU Core" from AMD/NVIDIA
                    elseif (stripos($text, 'GPU Core') !== false) {
                        $temps['GPU'] = $value;
                    }
                    // GFX from AMD CPU
                    elseif ($text === 'GFX' && !isset($temps['GPU'])) {
                        $temps['GPU (Integrated)'] = $value;
                    }
                    // Motherboard - "Temperature #1" from Nuvoton chip
                    elseif ($text === 'Temperature #1') {
                        $temps['Motherboard'] = $value;
                    }
                    // Storage drives - based on parent hardware name
                    elseif ($text === 'Temperature' && !empty($parentName)) {
                        if (stripos($parentName, 'SPCC') !== false) {
                            $temps['SSD (C:)'] = $value;
                        } elseif (stripos($parentName, 'WDC WD10EACS') !== false) {
                            $temps['HDD (I: or E:)'] = $value;
                        } elseif (stripos($parentName, 'SN7100') !== false || stripos($parentName, 'NVMe') !== false) {
                            $temps['NVMe (G:)'] = $value;
                        } elseif (stripos($parentName, 'ST3500413AS') !== false) {
                            $temps['HDD (F:)'] = $value;
                        }
                    }
                }
                
                // === FANS ===
                if (stripos($valueStr, 'RPM') !== false && $value > 0) {
                    // Match Fan #2, #3, #5, #6 exactly
                    if (preg_match('/^Fan #(\d+)$/i', $text, $m)) {
                        $fanNum = $m[1];
                        // Only store fans 2, 3, 5, 6
                        if (in_array($fanNum, ['2', '3', '5', '6'])) {
                            $fans['fan_' . $fanNum] = $value;
                        }
                    }
                }
            }
            
            // Recurse through children
            if (isset($node['Children'])) {
                $currentParent = isset($node['Text']) ? $node['Text'] : $parentName;
                foreach ($node['Children'] as $child) {
                    parseLHM($child, $temps, $fans, $gpuUsage, $gpuMemUsed, $gpuMemTotal, $cpuUsage, $memoryUsed, $memoryFree, $currentParent);
                }
            }
        }
        
        if (isset($lhm['Children'])) {
            foreach ($lhm['Children'] as $hardware) {
                parseLHM($hardware, $temps, $fans, $gpuUsage, $gpuMemUsed, $gpuMemTotal, $cpuUsage, $memoryUsed, $memoryFree);
            }
        }
        
        $stats['temperatures'] = $temps;
        $stats['fans'] = $fans;
        $stats['gpu_usage'] = $gpuUsage;
        $stats['gpu_memory_used_mb'] = $gpuMemUsed;
        $stats['gpu_memory_total_mb'] = $gpuMemTotal;
		$stats['cpu_usage'] = $cpuUsage;
		
		if (isset($memoryUsed) && isset($memoryFree)) {
			$stats['memory_used_gb']  = round($memoryUsed, 2);
			$stats['memory_free_gb']  = round($memoryFree, 2);
			$stats['memory_total_gb'] = round($memoryUsed + $memoryFree, 2);
			$stats['memory_percent']  = round(($memoryUsed / $stats['memory_total_gb']) * 100, 1);
	}}
		
    // === SYSTEM UPTIME ===
	// Local boot time (formatted)
	//exec('powershell -command "(Get-CimInstance Win32_OperatingSystem).LastBootUpTime | ForEach-Object { ([DateTime]$_).ToString(\'dddd, MMMM dd, yyyy h:mm:ss tt\') }"', $bootOutputLocal);
	safe_exec('powershell -command "(Get-CimInstance Win32_OperatingSystem).LastBootUpTime | ForEach-Object { ([DateTime]$_).ToString(\'dddd, MMMM dd, yyyy h:mm:ss tt\') }"', $bootOutputLocal);
	$stats['system_uptime_Raw_Local'] = $bootOutputLocal[0] ?? null;

	// Pull boot time converted to UTC
	//exec('powershell -command "(Get-CimInstance Win32_OperatingSystem).LastBootUpTime | ForEach-Object { ([DateTime]$_).ToUniversalTime().ToString(\'yyyy-MM-dd HH:mm:ss\') }"', $bootOutputUtc);
	safe_exec('powershell -command "(Get-CimInstance Win32_OperatingSystem).LastBootUpTime | ForEach-Object { ([DateTime]$_).ToUniversalTime().ToString(\'yyyy-MM-dd HH:mm:ss\') }"', $bootOutputUtc);

	$bootRawUtc = $bootOutputUtc[0] ?? null;
	$stats['system_uptime_Raw_UTC'] = $bootRawUtc;

    $bootDateUtc = new DateTime($bootRawUtc, new DateTimeZone('UTC'));
    $uptime = time() - $bootDateUtc->getTimestamp();

    $days = floor($uptime / 86400);
    $hours = floor(($uptime % 86400) / 3600);
    $minutes = floor(($uptime % 3600) / 60);

    $stats['system_uptime'] = sprintf('%dd %dh %dm', $days, $hours, $minutes);
    $stats['system_uptime_seconds'] = $uptime;
    
    // === ARK SERVER PROCESSES ===
	// Initialize stats
	$stats['ark_servers'] = [];
	
	// Path to the JSON file created by the batch script
	$jsonFile = 'H:/ARK/server_info.json';
	
	// Try to read JSON file
	$serverInfo = null;
	if (file_exists($jsonFile)) {
		$jsonContent = file_get_contents($jsonFile);
		// Strip BOM if present and trim whitespace
		$jsonContent = preg_replace('/^\xEF\xBB\xBF/', '', $jsonContent);
		$jsonContent = trim($jsonContent);
		$serverInfo = json_decode($jsonContent, true);
	}
	
	// PowerShell command to get all ShooterGameServer processes
	$psCommand = 'powershell -NoProfile -Command "Get-Process ShooterGameServer | Select-Object Id, WS, StartTime | ConvertTo-Json -Compress"';
	//exec($psCommand, $output, $retCode);
	safe_exec($psCommand, $output, $retCode);
	
	if ($retCode !== 0) {
		error_log('PowerShell command failed: '.implode("\n", $output));
	} else {
		$psOutput = implode("\n", $output);
		$processes = json_decode($psOutput, true);
		
		// Ensure $processes is an array
		if ($processes && !empty($processes)) {
			if (isset($processes['Id'])) {
				$processes = [$processes];
			}
			
			// Create lookup from JSON file (if available)
			$nameByPid = [];
			if ($serverInfo !== null) {
				// Handle single server case - convert to array
				$serverList = isset($serverInfo['pid']) ? [$serverInfo] : $serverInfo;
				
				foreach ($serverList as $server) {
					if (isset($server['pid']) && isset($server['server_name'])) {
						$nameByPid[$server['pid']] = $server['server_name'];
					}
				}
			}
			
			foreach ($processes as $proc) {
				$pid = $proc['Id'];
				
				// Get server name from JSON lookup
				$serverName = isset($nameByPid[$pid]) ? $nameByPid[$pid] : 'Unknown Server';
				
				// Memory in MB
				$memoryMB = isset($proc['WS']) ? round($proc['WS'] / 1024 / 1024, 2) : 0;
				
				// Uptime calculation
				$uptime = 'Unknown';
				if (!empty($proc['StartTime'])) {
					$timestamp = parseDotNetDate($proc['StartTime']);
					if ($timestamp !== null) {
						$uptimeSeconds = time() - $timestamp;
						$days = floor($uptimeSeconds / 86400);
						$hours = floor(($uptimeSeconds % 86400) / 3600);
						$minutes = floor(($uptimeSeconds % 3600) / 60);
						$uptime = sprintf('%dd %dh %dm', $days, $hours, $minutes);
					}
				}
				
				// Add to stats
				$stats['ark_servers'][] = [
					'name' => $serverName,
					'pid' => $pid,
					'memory_mb' => $memoryMB,
					'uptime' => $uptime,
				];
			}
		}
	}    
	
	
    // Output the $stats array as readable JSON for debugging or API response, then terminate script execution
	echo json_encode($stats, JSON_PRETTY_PRINT);
	exit;
}

$pageTitle = 'System Monitor';
include '../includes/header.php';

// Get scheduled restart info
$restartInfo = getScheduledRestartInfo();

//echo 'Current user: ' . get_current_user() . PHP_EOL;
//echo 'Effective user: ' . exec('whoami') . PHP_EOL;

?>

<div class="page-header">
    <h1>📊 System Monitor</h1>
    <p>Task Manager - Performance Tab</p>
</div>

<div class="monitor-container">
    <div class="monitor-controls">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" class="btn btn-primary">
                ⏸️ Pause
            </button>
            <label style="color: white;">
                Refresh Interval:
                <input type="number" 
                       id="refreshInterval" 
                       value="3" 
                       min="1" 
                       max="60" 
                       style="width: 60px; padding: 0.5rem; margin-left: 0.5rem;"
                       onchange="updateRefreshInterval()">
                seconds
            </label>
        </div>
        <span id="lastUpdate" style="color: white;">
            Last Update: <span id="updateTime">--:--:--</span>
        </span>
    </div>

    <!-- Scheduled Restart Info -->
    <div class="restart-schedule">
        <h2>⏰ Scheduled Restart Times</h2>
        <div class="schedule-info-row">
            <span class="schedule-item">
                <span class="schedule-label">Recurrence</span>
                <span class="schedule-equals">=</span>
                <span class="schedule-value"><?php echo htmlspecialchars($restartInfo['recurrence']); ?></span>
            </span>
            <span class="schedule-item">
                <span class="schedule-label">Shutdown</span>
                <span class="schedule-equals">=</span>
                <span class="schedule-value"><?php echo htmlspecialchars($restartInfo['shutdown_time']); ?></span>
            </span>
            <span class="schedule-item">
                <span class="schedule-label">Startup</span>
                <span class="schedule-equals">=</span>
                <span class="schedule-value"><?php echo htmlspecialchars($restartInfo['startup_time']); ?></span>
            </span>
        </div>
        <div class="days-calendar">
            <div class="days-label">Restart Days:</div>
            <div class="days-grid">
                <?php
                $allDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach ($allDays as $day):
                    $isActive = in_array($day, $restartInfo['days']);
                    $class = $isActive ? 'day-active' : 'day-inactive';
                ?>
                    <div class="day-box <?php echo $class; ?>">
                        <div class="day-name"><?php echo $day; ?></div>
                        <div class="day-status"><?php echo $isActive ? '✓' : ''; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="alert alert-warning" style="margin-top: 1rem;">
            ⚠️ <strong>Note:</strong> Server will shutdown at <?php echo $restartInfo['shutdown_time']; ?> and restart at <?php echo $restartInfo['startup_time']; ?> 
            on <?php echo implode(' and ', $restartInfo['days']); ?>.
        </div>
    </div>

    <!-- Performance Grid -->
    <div class="performance-grid">
        <!-- CPU -->
        <div class="perf-card">
            <h3>💻 CPU</h3>
            <div class="metric-row">
                <span class="metric-label">Usage:</span>
                <span class="metric-value" id="cpuValue">--%</span>
            </div>
            <div class="perf-bar">
                <div id="cpuBar" class="perf-bar-fill"></div>
            </div>
            <div class="metric-row">
                <span class="metric-label">Temp:</span>
                <span class="metric-value" id="cpuTemp">--°C</span>
            </div>
        </div>

        <!-- Memory -->
        <div class="perf-card">
            <h3>🧠 Memory</h3>
            <div class="metric-row">
                <span class="metric-label">Usage:</span>
                <span class="metric-value" id="memValue">--%</span>
            </div>
            <div class="perf-bar">
                <div id="memBar" class="perf-bar-fill"></div>
            </div>
            <div class="metric-row">
                <span class="metric-label">Used/Total:</span>
                <span class="metric-value" id="memDetail">-- / -- GB</span>
            </div>
        </div>

        <!-- GPU -->
        <div class="perf-card">
            <h3>🎮 GPU</h3>
            <div class="metric-row">
                <span class="metric-label">Usage:</span>
                <span class="metric-value" id="gpuValue">--%</span>
            </div>
            <div class="perf-bar">
                <div id="gpuBar" class="perf-bar-fill"></div>
            </div>
            <div class="metric-row">
                <span class="metric-label">Temp:</span>
                <span class="metric-value" id="gpuTemp">--°C</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Memory:</span>
                <span class="metric-value" id="gpuMem">-- MB</span>
            </div>
        </div>

        <!-- Network -->
        <div class="perf-card">
            <h3>🌐 Network</h3>
            <div class="metric-row">
                <span class="metric-label">Received:</span>
                <span class="metric-value" id="netDown">-- KB/s</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Sent:</span>
                <span class="metric-value" id="netUp">-- KB/s</span>
            </div>
        </div>
    </div>

    <!-- Disks -->
    <div class="section">
        <h2>💾 Disk Usage</h2>
        <div id="diskGrid" class="disk-grid"></div>
    </div>

    <!-- Temperatures -->
    <div class="section">
        <h2>🌡️ Temperatures</h2>
        <div id="tempGrid" class="temp-grid"></div>
    </div>

    <!-- Fans -->
    <div class="section">
        <h2>🌀 Fan Speeds</h2>
        <div id="fanGrid" class="fan-grid"></div>
    </div>

    <!-- Uptime -->
    <div class="section">
        <h2>⏰ Uptime</h2>
        <div class="uptime-grid">
            <div class="uptime-item">
                <strong>System:</strong>
                <span id="systemUptime">--</span>
            </div>
        </div>
    </div>

    <!-- ARK Servers -->
    <div class="section">
        <h2>🦖 ARK Server Processes</h2>
        <div id="arkServers" class="ark-grid"></div>
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
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.performance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.perf-card {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.perf-card h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-size: 1.3rem;
}

.metric-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0.8rem 0;
    font-size: 1.3rem;
}

.metric-label {
    color: #ff8c00;
    font-weight: bold;
}

.metric-value {
    color: #00ff00;
    font-weight: bold;
}

.perf-bar {
    height: 20px;
    background: var(--darker-bg);
    border-radius: 10px;
    overflow: hidden;
    margin: 1rem 0;
}

.perf-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    transition: width 0.5s ease;
    width: 0%;
}

.section {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-color);
}

.section h2 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.disk-grid, .temp-grid, .fan-grid, .ark-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.disk-item, .temp-item, .fan-item, .ark-item {
    background: var(--darker-bg);
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.uptime-grid {
    display: grid;
    gap: 1rem;
}

.uptime-item {
    background: var(--darker-bg);
    padding: 1rem;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
}

.restart-schedule {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    border: 2px solid var(--warning-color);
}

.restart-schedule h2 {
    color: var(--warning-color);
    margin-bottom: 1rem;
    text-align: center;
}

.schedule-info-row {
    display: flex;
    justify-content: space-around;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.schedule-item {
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.schedule-label {
    color: #ff8c00;
    font-weight: bold;
}

.schedule-equals {
    color: #00ff00;
    font-weight: bold;
}

.schedule-value {
    color: #00ff00;
    font-weight: bold;
}

.days-calendar {
    width: 100%;
}

.days-label {
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--text-primary);
    margin-bottom: 1rem;
    text-align: center;
}

.days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1rem;
    width: 100%;
}

.day-box {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s;
}

.day-active {
    background: var(--success-color);
    color: white;
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
}

.day-inactive {
    background: var(--darker-bg);
    color: #666;
    border: 2px solid #444;
}

.day-name {
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.day-status {
    font-size: 1.5rem;
    font-weight: bold;
}

@media (max-width: 768px) {
    .days-grid {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .schedule-info-row {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<script>
let autoRefreshEnabled = true;
let autoRefreshInterval = null;
let refreshSeconds = 3;
let lastNetworkData = null;

async function fetchStats() {
    try {
        const response = await fetch('?ajax=stats');
        const stats = await response.json();
        
        // CPU
        if (stats.cpu_usage !== undefined) {
            document.getElementById('cpuValue').textContent = stats.cpu_usage + '%';
            document.getElementById('cpuBar').style.width = stats.cpu_usage + '%';
        }
        
        // CPU Temperature
        if (stats.temperatures && stats.temperatures['CPU Package']) {
            document.getElementById('cpuTemp').textContent = stats.temperatures['CPU Package'] + '°C';
        }
        
        // Memory
        if (stats.memory_percent !== undefined) {
            document.getElementById('memValue').textContent = stats.memory_percent + '%';
            document.getElementById('memBar').style.width = stats.memory_percent + '%';
            document.getElementById('memDetail').textContent = 
                stats.memory_used_gb + ' / ' + stats.memory_total_gb + ' GB';
        }
        
        // GPU Usage
        if (stats.gpu_usage !== undefined && stats.gpu_usage !== null) {
            document.getElementById('gpuValue').textContent = stats.gpu_usage + '%';
            document.getElementById('gpuBar').style.width = stats.gpu_usage + '%';
        }
        
        // GPU Temperature  
        if (stats.temperatures && stats.temperatures['GPU']) {
            document.getElementById('gpuTemp').textContent = stats.temperatures['GPU'] + '°C';
        }
        
        // GPU Memory
        if (stats.gpu_memory_used_mb && stats.gpu_memory_total_mb) {
            document.getElementById('gpuMem').textContent = 
                Math.round(stats.gpu_memory_used_mb) + ' / ' + Math.round(stats.gpu_memory_total_mb) + ' MB';
        }
        
        // Network Speed (calculate from difference)
        if (stats.network_received_bytes !== undefined) {
            if (lastNetworkData) {
                const timeDiff = refreshSeconds;
                const downSpeed = (stats.network_received_bytes - lastNetworkData.received) / timeDiff / 1024;
                const upSpeed = (stats.network_sent_bytes - lastNetworkData.sent) / timeDiff / 1024;
                
                document.getElementById('netDown').textContent = 
                    downSpeed > 1024 ? (downSpeed / 1024).toFixed(2) + ' MB/s' : downSpeed.toFixed(2) + ' KB/s';
                document.getElementById('netUp').textContent = 
                    upSpeed > 1024 ? (upSpeed / 1024).toFixed(2) + ' MB/s' : upSpeed.toFixed(2) + ' KB/s';
            }
            
            lastNetworkData = {
                received: stats.network_received_bytes,
                sent: stats.network_sent_bytes
            };
        }
        
        // Disks
        if (stats.disks && stats.disks.length > 0) {
            const diskHtml = stats.disks.map(disk => `
                <div class="disk-item">
                    <div style="font-size: 1.3rem; margin-bottom: 0.5rem;">
                        <span style="color: #ff8c00; font-weight: bold;">Drive:</span>
                        <span style="color: #00ff00; font-weight: bold;">${disk.drive}</span>
                    </div>
                    <div style="font-size: 1.3rem; margin-bottom: 0.5rem;">
                        <span style="color: #ff8c00; font-weight: bold;">Usage:</span>
                        <span style="color: #00ff00; font-weight: bold;">${disk.used_percent}%</span>
                    </div>
                    <div class="perf-bar" style="height: 15px; margin: 0.5rem 0;">
                        <div class="perf-bar-fill" style="width: ${disk.used_percent}%;"></div>
                    </div>
                    <div style="font-size: 1.1rem;">
                        <span style="color: #ff8c00; font-weight: bold;">Space:</span>
                        <span style="color: #00ff00; font-weight: bold;">${disk.used_gb} / ${disk.total_gb} GB</span>
                    </div>
                </div>
            `).join('');
            document.getElementById('diskGrid').innerHTML = diskHtml;
        }
        
        // Temperatures
        if (stats.temperatures) {
            const tempHtml = Object.entries(stats.temperatures)
                .sort((a, b) => a[0].localeCompare(b[0]))
                .map(([key, value]) => `
                    <div class="temp-item">
                        <div style="font-size: 1.3rem; margin-bottom: 0.5rem;">
                            <span style="color: #ff8c00; font-weight: bold;">${key}:</span>
                        </div>
                        <div style="font-size: 1.8rem;">
                            <span style="color: ${value > 70 ? 'var(--danger-color)' : value > 50 ? 'var(--warning-color)' : '#00ff00'}; font-weight: bold;">
                                ${value}°C
                            </span>
                        </div>
                    </div>
                `).join('');
            document.getElementById('tempGrid').innerHTML = tempHtml || '<p style="color: #999;">No temperature data available. Make sure LibreHardwareMonitor is running.</p>';
        }
        
        // Fans
        if (stats.fans) {
            const fanLabels = {
                'fan_2': 'Fan #2',
                'fan_3': 'Fan #3',
                'fan_5': 'Fan #5',
                'fan_6': 'Fan #6'
            };
            
            const fanHtml = Object.entries(stats.fans)
                .sort((a, b) => {
                    const numA = parseInt(a[0].match(/\d+/)?.[0] || 999);
                    const numB = parseInt(b[0].match(/\d+/)?.[0] || 999);
                    return numA - numB;
                })
                .map(([key, rpm]) => `
                    <div class="fan-item">
                        <div style="font-size: 1.3rem; margin-bottom: 0.5rem;">
                            <span style="color: #ff8c00; font-weight: bold;">${fanLabels[key] || key}:</span>
                        </div>
                        <div style="font-size: 1.8rem;">
                            <span style="color: ${rpm > 0 ? '#00ff00' : 'var(--danger-color)'}; font-weight: bold;">
                                ${Math.round(rpm)} RPM
                            </span>
                        </div>
                    </div>
                `).join('');
            document.getElementById('fanGrid').innerHTML = fanHtml || '<p style="color: #999;">No fan data available</p>';
        }
        
        // System Uptime
        if (stats.system_uptime) {
            document.getElementById('systemUptime').textContent = stats.system_uptime;
        }
        
        // ARK Servers
        if (stats.ark_servers && stats.ark_servers.length > 0) {
            const arkHtml = stats.ark_servers.map(server => `
                <div class="ark-item">
                    <div style="font-size: 1.3rem; margin-bottom: 0.5rem;">
                        <span style="color: #ff8c00; font-weight: bold;">Server:</span>
                        <span style="color: #00ff00; font-weight: bold;">${server.name}</span>
                    </div>
                    <div style="font-size: 1.1rem; margin-bottom: 0.3rem;">
                        <span style="color: #ff8c00; font-weight: bold;">PID:</span>
                        <span style="color: #00ff00; font-weight: bold;">${server.pid}</span>
                    </div>
                    <div style="font-size: 1.1rem; margin-bottom: 0.3rem;">
                        <span style="color: #ff8c00; font-weight: bold;">Memory:</span>
                        <span style="color: #00ff00; font-weight: bold;">${server.memory_mb} MB</span>
                    </div>
                    <div style="font-size: 1.1rem;">
                        <span style="color: #ff8c00; font-weight: bold;">Uptime:</span>
                        <span style="color: #00ff00; font-weight: bold;">${server.uptime}</span>
                    </div>
                </div>
            `).join('');
            document.getElementById('arkServers').innerHTML = arkHtml;
        } else {
            document.getElementById('arkServers').innerHTML = '<p style="color: #999;">No ARK servers running</p>';
        }
        
        document.getElementById('updateTime').textContent = new Date().toLocaleTimeString();
        
    } catch (error) {
        console.error('Failed to fetch stats:', error);
    }
}

function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;
    const btn = document.getElementById('autoRefreshBtn');
    
    if (autoRefreshEnabled) {
        btn.textContent = '⏸️ Pause';
        startAutoRefresh();
    } else {
        btn.textContent = '▶️ Resume';
        stopAutoRefresh();
    }
}

function updateRefreshInterval() {
    refreshSeconds = parseInt(document.getElementById('refreshInterval').value);
    if (autoRefreshEnabled) {
        stopAutoRefresh();
        startAutoRefresh();
    }
}

function startAutoRefresh() {
    if (autoRefreshInterval) return;
    fetchStats();
    autoRefreshInterval = setInterval(fetchStats, refreshSeconds * 1000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

// Start on page load
startAutoRefresh();

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
    } else if (autoRefreshEnabled) {
        startAutoRefresh();
    }
});
</script>

<?php include '../includes/footer.php'; ?>