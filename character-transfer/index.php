<?php
/**
 * Character Transfer Tool
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check permission
if (!hasPermission('view_character_transfer')) {
    die('Access denied');
}

// Handle transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'transfer') {
    if (!hasPermission('transfer_characters')) {
        $_SESSION['error'] = 'You do not have permission to transfer characters';
        redirect('./');
    }
    
    $sourceServer = $_POST['source_server'] ?? '';
    $targetServer = $_POST['target_server'] ?? '';
    $selectedFiles = $_POST['characters'] ?? [];
    
    if (!validateServer($sourceServer) || !validateServer($targetServer)) {
        $_SESSION['error'] = 'Invalid server selected';
        redirect('./');
    }
    
    if ($sourceServer === $targetServer) {
        $_SESSION['error'] = 'Source and target servers cannot be the same';
        redirect('./');
    }
    
    if (empty($selectedFiles)) {
        $_SESSION['error'] = 'No characters selected for transfer';
        redirect('./');
    }
    
    $sourceInfo = getServerInfo($sourceServer);
    $targetInfo = getServerInfo($targetServer);
    
    $sourcePath = ARK_SAVED_DIR . DIRECTORY_SEPARATOR . $sourceInfo['save_dir'];
    $targetPath = ARK_SAVED_DIR . DIRECTORY_SEPARATOR . $targetInfo['save_dir'];
    
    // Create target directory if it doesn't exist
    if (!is_dir($targetPath)) {
        mkdir($targetPath, 0755, true);
    }
    
    $transferred = 0;
    $errors = [];
    
    foreach ($selectedFiles as $filename) {
        // Sanitize filename
        $filename = basename($filename);
        
        if (!preg_match('/\.arkprofile$/i', $filename)) {
            $errors[] = "Invalid file: $filename";
            continue;
        }
        
        $sourceFile = $sourcePath . DIRECTORY_SEPARATOR . $filename;
        $targetFile = $targetPath . DIRECTORY_SEPARATOR . $filename;
        
        if (!file_exists($sourceFile)) {
            $errors[] = "Source file not found: $filename";
            continue;
        }
        
        // Create backup if file already exists in target
        if (file_exists($targetFile)) {
            $backupPath = createBackup($targetFile, 'character');
            if (!$backupPath) {
                $errors[] = "Failed to backup existing file: $filename";
                continue;
            }
        }
        
        // Copy the file
        if (copy($sourceFile, $targetFile)) {
            $transferred++;
            
            // Also copy tribe file if it exists
            $tribeFile = str_replace('.arkprofile', '.arktribe', $filename);
            $sourceTribe = $sourcePath . DIRECTORY_SEPARATOR . $tribeFile;
            $targetTribe = $targetPath . DIRECTORY_SEPARATOR . $tribeFile;
            
            if (file_exists($sourceTribe)) {
                copy($sourceTribe, $targetTribe);
            }
        } else {
            $errors[] = "Failed to copy: $filename";
        }
    }
    
    $message = "Transferred $transferred character(s) from {$sourceInfo['map']} to {$targetInfo['map']}";
    if (!empty($errors)) {
        $message .= ". Errors: " . implode(', ', $errors);
    }
    
    logAction('CHARACTER_TRANSFER', $message);
    
    if ($transferred > 0) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = 'Transfer failed: ' . implode(', ', $errors);
    }
    
    redirect('./');
}

// Get server selections
$sourceServer = $_GET['source'] ?? 'extinction';
$targetServer = $_GET['target'] ?? 'fjordur';

if (!validateServer($sourceServer)) $sourceServer = 'extinction';
if (!validateServer($targetServer)) $targetServer = 'fjordur';

$sourceInfo = getServerInfo($sourceServer);
$targetInfo = getServerInfo($targetServer);

// Get character files from source and target
$sourceCharacters = getCharacterFiles($sourceInfo['save_dir']);
$targetCharacters = getCharacterFiles($targetInfo['save_dir']);

// Create a map of target characters by steam ID for easy lookup
$targetCharMap = [];
foreach ($targetCharacters as $char) {
    $targetCharMap[$char['steam_id']] = $char;
}

$pageTitle = 'Character Transfer';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>🔄 Character Transfer</h1>
    <p>Transfer characters between servers</p>
</div>

<div class="transfer-container">
    <div class="transfer-panel">
        <h2>Select Servers</h2>
        <form method="GET" class="server-selection-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Source Server:</label>
                    <select name="source" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($SERVERS as $key => $server): ?>
                            <option value="<?php echo $key; ?>" <?php echo $sourceServer === $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($server['map']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-arrow">→</div>
                
                <div class="form-group">
                    <label>Target Server:</label>
                    <select name="target" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($SERVERS as $key => $server): ?>
                            <option value="<?php echo $key; ?>" <?php echo $targetServer === $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($server['map']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <?php if ($sourceServer === $targetServer): ?>
        <div class="alert alert-error">
            ⚠️ Source and target servers cannot be the same. Please select different servers.
        </div>
    <?php else: ?>
        <form method="POST" onsubmit="return confirmTransfer();">
            <input type="hidden" name="action" value="transfer">
            <input type="hidden" name="source_server" value="<?php echo htmlspecialchars($sourceServer); ?>">
            <input type="hidden" name="target_server" value="<?php echo htmlspecialchars($targetServer); ?>">
            
            <div class="characters-panel">
                <div class="panel-header">
                    <h3>📋 Characters on <?php echo htmlspecialchars($sourceInfo['map']); ?></h3>
                    <?php if (hasPermission('transfer_characters')): ?>
                    <div class="select-actions">
                        <button type="button" onclick="selectAll()" class="btn btn-sm btn-secondary">Select All</button>
                        <button type="button" onclick="selectNone()" class="btn btn-sm btn-secondary">Select None</button>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($sourceCharacters)): ?>
                    <div class="alert alert-info">
                        No character files found on source server.
                    </div>
                <?php else: ?>
                    <div class="characters-list">
                        <?php foreach ($sourceCharacters as $char): ?>
                            <div class="character-item-extended">
                                <?php if (hasPermission('transfer_characters')): ?>
                                <label class="character-checkbox">
                                    <input type="checkbox" 
                                           name="characters[]" 
                                           value="<?php echo htmlspecialchars($char['filename']); ?>">
                                </label>
                                <?php endif; ?>
                                
                                <div class="character-comparison">
                                    <!-- Source Character Info -->
                                    <div class="character-source">
                                        <h4 style="color: var(--primary-color);">
                                            <?php echo htmlspecialchars($char['display_name']); ?>
                                        </h4>
                                        <div class="character-label">📤 Source Server: <?php echo htmlspecialchars($sourceInfo['map']); ?></div>
                                        
                                        <div class="character-details-extended">
                                            <div class="detail-item">
                                                <span class="label">File:</span>
                                                <span class="value"><?php echo htmlspecialchars($char['filename']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="label">Steam ID:</span>
                                                <span class="value"><?php echo htmlspecialchars($char['steam_id']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="label">Size:</span>
                                                <span class="value"><?php echo formatFileSize($char['size']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="label">Modified:</span>
                                                <span class="value"><?php echo date('Y-m-d H:i:s', $char['modified']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="label">Path:</span>
                                                <span class="value" style="font-size: 0.85rem; word-break: break-all;">
                                                    <?php echo htmlspecialchars($char['file']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <?php if (hasPermission('download_characters')): ?>
                                        <div class="character-actions">
                                            <a href="download.php?server=<?php echo urlencode($sourceServer); ?>&file=<?php echo urlencode($char['filename']); ?>" 
                                               class="btn btn-sm btn-info">
                                                📥 Download Source
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Arrow -->
                                    <div class="transfer-arrow">→</div>
                                    
                                    <!-- Target Character Info (if exists) -->
                                    <div class="character-target">
                                        <?php if (isset($targetCharMap[$char['steam_id']])): ?>
                                            <?php $targetChar = $targetCharMap[$char['steam_id']]; ?>
                                            <h4 style="color: var(--warning-color);">
                                                <?php echo htmlspecialchars($targetChar['display_name']); ?>
                                            </h4>
                                            <div class="character-label">📥 Target Server: <?php echo htmlspecialchars($targetInfo['map']); ?></div>
                                            
                                            <div class="character-details-extended">
                                                <div class="detail-item">
                                                    <span class="label">File:</span>
                                                    <span class="value"><?php echo htmlspecialchars($targetChar['filename']); ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="label">Steam ID:</span>
                                                    <span class="value"><?php echo htmlspecialchars($targetChar['steam_id']); ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="label">Size:</span>
                                                    <span class="value"><?php echo formatFileSize($targetChar['size']); ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="label">Modified:</span>
                                                    <span class="value"><?php echo date('Y-m-d H:i:s', $targetChar['modified']); ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="label">Path:</span>
                                                    <span class="value" style="font-size: 0.85rem; word-break: break-all;">
                                                        <?php echo htmlspecialchars($targetChar['file']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="character-warning">
                                                ⚠️ Exists on target - Will be backed up before overwrite
                                            </div>
                                            
                                            <?php if (hasPermission('download_characters')): ?>
                                            <div class="character-actions">
                                                <a href="download.php?server=<?php echo urlencode($targetServer); ?>&file=<?php echo urlencode($targetChar['filename']); ?>" 
                                                   class="btn btn-sm btn-warning">
                                                    📥 Download Target
                                                </a>
                                            </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <h4 style="color: var(--text-secondary);">New Character</h4>
                                            <div class="character-label">📥 Target Server: <?php echo htmlspecialchars($targetInfo['map']); ?></div>
                                            <p style="color: var(--text-secondary); margin-top: 1rem;">
                                                ✅ Character does not exist on target server.<br>
                                                Will be created as new.
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (hasPermission('transfer_characters')): ?>
                    <div class="transfer-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            🔄 Transfer Selected Characters
                        </button>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </form>

        <div class="transfer-info">
            <h3>ℹ️ Transfer Information</h3>
            <ul>
                <li>✅ Character files (.arkprofile) will be copied to the target server</li>
                <li>✅ Tribe files (.arktribe) will be included if they exist</li>
                <li>✅ Existing files on target will be backed up automatically to: <?php echo htmlspecialchars(BACKUP_DIR); ?></li>
                <li>⚠️ Servers should be stopped during transfer for safety</li>
                <li>⚠️ Players must log out before transferring their characters</li>
                <li>📥 Download backups before transferring to save locally</li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<style>
.character-item-extended {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border: 2px solid var(--border-color);
}

.character-checkbox {
    display: block;
    margin-bottom: 1rem;
}

.character-checkbox input {
    width: 24px;
    height: 24px;
    cursor: pointer;
}

.character-comparison {
    display: grid;
    grid-template-columns: 1fr 50px 1fr;
    gap: 1rem;
    align-items: start;
}

.character-source, .character-target {
    background: var(--darker-bg);
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.character-label {
    background: var(--primary-color);
    color: white;
    padding: 0.5rem;
    border-radius: 4px;
    font-weight: bold;
    margin-bottom: 1rem;
    text-align: center;
}

.character-details-extended {
    margin: 1rem 0;
}

.detail-item {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 0.5rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-item .label {
    font-weight: bold;
    color: var(--text-secondary);
}

.detail-item .value {
    color: var(--text-primary);
}

.transfer-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--primary-color);
}

.character-actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

@media (max-width: 1024px) {
    .character-comparison {
        grid-template-columns: 1fr;
    }
    
    .transfer-arrow {
        transform: rotate(90deg);
        margin: 1rem 0;
    }
}
</style>

<script>
function selectAll() {
    document.querySelectorAll('.character-item-extended input[type="checkbox"]').forEach(cb => cb.checked = true);
}

function selectNone() {
    document.querySelectorAll('.character-item-extended input[type="checkbox"]').forEach(cb => cb.checked = false);
}

function confirmTransfer() {
    const checked = document.querySelectorAll('.character-item-extended input[type="checkbox"]:checked');
    if (checked.length === 0) {
        alert('Please select at least one character to transfer.');
        return false;
    }
    
    const count = checked.length;
    const source = '<?php echo addslashes($sourceInfo['map']); ?>';
    const target = '<?php echo addslashes($targetInfo['map']); ?>';
    
    return confirm(`Transfer ${count} character(s) from ${source} to ${target}?\n\nExisting files will be backed up automatically.`);
}
</script>

<?php include '../includes/footer.php'; ?>