<?php
/**
 * INI File Editor - Key/Value Interface
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check permission
if (!hasPermission('view_ini_editor')) {
    die('Access denied');
}

// Determine which INI files user can see
$allowedInis = [];
foreach ($INI_FILES as $name => $path) {
    $iniKey = strtolower(str_replace('.ini', '', $name));
    $permission = 'edit_ini_' . $iniKey;
    
    if (hasPermission('all') || hasPermission($permission) || hasPermission('edit_ini_limited')) {
        $allowedInis[$name] = $path;
    }
}

if (empty($allowedInis)) {
    die('No INI files accessible with your permissions');
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $file = $_POST['file'] ?? '';
    $changes = $_POST['keys'] ?? [];
    
    if (!isset($allowedInis[$file])) {
        $_SESSION['error'] = 'You do not have permission to edit this file';
        redirect('?file=' . urlencode($file));
    }
    
    $filepath = $allowedInis[$file];
    
    if (!file_exists($filepath)) {
        $_SESSION['error'] = 'File not found';
        redirect('?file=' . urlencode($file));
    }
    
    // Create backup
    $backupPath = createBackup($filepath, 'ini');
    
    if (!$backupPath) {
        $_SESSION['error'] = 'Failed to create backup';
        redirect('?file=' . urlencode($file));
    }
    
    // Read current INI file
    $iniContent = file_get_contents($filepath);
    $lines = explode("\n", $iniContent);
    
    $updated = 0;
    $newLines = [];
    $currentSection = '';
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        
        // Track current section
        if (preg_match('/^\[(.+)\]$/', $trimmed, $matches)) {
            $currentSection = $matches[1];
            $newLines[] = $line;
            continue;
        }
        
        // Check if this is a key=value line
        if (preg_match('/^([^=]+)=(.*)$/', $trimmed, $matches)) {
            $key = trim($matches[1]);
            $oldValue = $matches[2];
            
            // Check if we have a new value for this key
            if (isset($changes[$key])) {
                $newValue = $changes[$key];
                
                // Only update if value actually changed
                if ($newValue !== $oldValue) {
                    // Preserve the indentation/formatting
                    $newLines[] = preg_replace('/=.*$/', '=' . $newValue, $line);
                    $updated++;
                } else {
                    $newLines[] = $line;
                }
            } else {
                $newLines[] = $line;
            }
        } else {
            // Comment or empty line
            $newLines[] = $line;
        }
    }
    
    // Write updated content
    $newContent = implode("\n", $newLines);
    
    if (file_put_contents($filepath, $newContent) !== false) {
        logAction('INI_EDIT', "Edited $file - $updated key(s) updated");
        $_SESSION['success'] = "Successfully saved $file - $updated setting(s) updated (Backup: " . basename($backupPath) . ")";
    } else {
        $_SESSION['error'] = "Failed to save $file";
    }
    
    redirect('?file=' . urlencode($file));
}

// Get selected file
$selectedFile = $_GET['file'] ?? array_key_first($allowedInis);

if (!isset($allowedInis[$selectedFile])) {
    $selectedFile = array_key_first($allowedInis);
}

$filepath = $allowedInis[$selectedFile];

// Get visible keys for this user and file
global $INI_VISIBLE_KEYS, $INI_KEY_DESCRIPTIONS;
$role = getCurrentUserRole();
$visibleKeys = $INI_VISIBLE_KEYS[$role][$selectedFile] ?? [];

// Parse INI file
$parsedIni = [];
$currentSection = 'General';

if (file_exists($filepath)) {
    $lines = file($filepath, FILE_IGNORE_NEW_LINES);
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        
        // Skip empty lines and comments
        if (empty($trimmed) || $trimmed[0] === ';' || $trimmed[0] === '#') {
            continue;
        }
        
        // Section header
        if (preg_match('/^\[(.+)\]$/', $trimmed, $matches)) {
            $currentSection = $matches[1];
            continue;
        }
        
        // Key=Value
        if (preg_match('/^([^=]+)=(.*)$/', $trimmed, $matches)) {
            $key = trim($matches[1]);
            $value = $matches[2];
            
            if (!isset($parsedIni[$currentSection])) {
                $parsedIni[$currentSection] = [];
            }
            
            $parsedIni[$currentSection][$key] = $value;
        }
    }
}

// Filter keys based on permissions
$displayKeys = [];

if ($visibleKeys === 'ALL') {
    // Admin sees everything
    $displayKeys = $parsedIni;
} else {
    // Filter to only visible keys
    foreach ($parsedIni as $section => $keys) {
        foreach ($keys as $key => $value) {
            if (in_array($key, $visibleKeys)) {
                if (!isset($displayKeys[$section])) {
                    $displayKeys[$section] = [];
                }
                $displayKeys[$section][$key] = $value;
            }
        }
    }
}

$fileInfo = file_exists($filepath) ? [
    'size' => filesize($filepath),
    'modified' => filemtime($filepath),
    'writable' => is_writable($filepath)
] : null;

$pageTitle = 'INI Editor';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>📝 INI File Editor</h1>
    <p>Edit server configuration settings</p>
</div>

<div class="ini-editor-container">
    <div class="file-selector">
        <h3>Select INI File:</h3>
        <div class="file-tabs">
            <?php foreach ($allowedInis as $name => $path): ?>
			<?php //print ('<PRE>'); var_dump($name); print ('</PRE>'); ?>
				<?php if (hasAccess('ini_visible_keys', $name)): ?>
					<a href="?file=<?php echo urlencode($name); ?>" 
					   class="file-tab <?php echo $selectedFile === $name ? 'active' : ''; ?>">
						<?php echo htmlspecialchars($name); ?>
					</a>
				<?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($fileInfo): ?>
        <div class="file-info">
            <span><strong>File:</strong> <?php echo htmlspecialchars($selectedFile); ?></span>
            <span><strong>Size:</strong> <?php echo formatFileSize($fileInfo['size']); ?></span>
            <span><strong>Modified:</strong> <?php echo date('Y-m-d H:i:s', $fileInfo['modified']); ?></span>
            <span><strong>Keys Shown:</strong> <?php echo count($displayKeys, COUNT_RECURSIVE) - count($displayKeys); ?></span>
        </div>
		
        <?php if (empty($displayKeys)): ?>
            <div class="alert alert-warning">
                ⚠️ No editable settings found in this file for your role. Contact an admin for access.
            </div>
        <?php else: ?>
            <form method="POST" class="ini-key-value-form">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="file" value="<?php echo htmlspecialchars($selectedFile); ?>">
                
                <div class="editor-toolbar">
                    <button type="submit" class="btn btn-success">💾 Save Changes</button>
                    <button type="button" class="btn btn-secondary" onclick="location.reload()">↺ Reset</button>
                </div>

                <div class="settings-container">
                    <?php foreach ($displayKeys as $section => $keys): ?>
                        <div class="settings-section">
                            <h2 class="section-header">[<?php echo htmlspecialchars($section); ?>]</h2>
                            
                            <div class="settings-grid">
                                <?php foreach ($keys as $key => $value): ?>
                                    <div class="setting-item">
                                        <div class="setting-header">
                                            <label for="key_<?php echo htmlspecialchars($key); ?>" class="setting-label">
                                                <?php echo htmlspecialchars($key); ?>:
                                            </label>
                                        </div>
                                        
                                        <div class="setting-input">
                                            <input type="text" 
                                                   id="key_<?php echo htmlspecialchars($key); ?>"
                                                   name="keys[<?php echo htmlspecialchars($key); ?>]" 
                                                   value="<?php echo htmlspecialchars($value); ?>"
                                                   class="form-control"
                                                   placeholder="Enter value...">
                                        </div>
                                        
                                        <div class="setting-description">
                                            <?php 
                                            $description = $INI_KEY_DESCRIPTIONS[$key] ?? 'No description available for this setting.';
                                            echo htmlspecialchars($description);
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="editor-toolbar">
                    <button type="submit" class="btn btn-success btn-lg">💾 Save All Changes</button>
                </div>
            </form>
        <?php endif; ?>

        <div class="editor-help">
            <h3>💡 Important Notes</h3>
            <ul>
                <li>⚠️ <strong>Server must be restarted</strong> for changes to take effect</li>
                <li>✅ Backups are automatically created before saving: <?php echo htmlspecialchars(BACKUP_DIR); ?></li>
                <li>📝 Only the settings you can see and edit are shown based on your role</li>
                <li>🔒 All other settings in the INI file remain unchanged</li>
                <?php if ($visibleKeys === 'ALL'): ?>
                <li>👑 <strong>Admin Mode:</strong> You can see and edit ALL settings in this file</li>
                <?php endif; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="alert alert-error">
            <strong>Error:</strong> File not found at <?php echo htmlspecialchars($filepath); ?>
        </div>
    <?php endif; ?>
</div>

<style>
.ini-key-value-form {
    width: 100%;
}

.settings-container {
    max-width: 1200px;
    margin: 2rem auto;
}

.settings-section {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
}

.section-header {
    color: var(--primary-color);
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-color);
}

.settings-grid {
    display: grid;
    gap: 2rem;
}

.setting-item {
    background: var(--darker-bg);
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    transition: border-color 0.3s;
}

.setting-item:hover {
    border-color: var(--primary-color);
}

.setting-header {
    margin-bottom: 0.75rem;
}

.setting-label {
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--text-primary);
    display: block;
}

.setting-input {
    margin-bottom: 0.75rem;
}

.setting-input .form-control {
    font-size: 1rem;
    padding: 0.75rem;
    background: #000;
    border: 2px solid var(--border-color);
    color: var(--text-primary);
    transition: border-color 0.3s;
}

.setting-input .form-control:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.setting-description {
    font-size: 0.9rem;
    color: var(--text-secondary);
    line-height: 1.5;
    font-style: italic;
    padding-left: 0.5rem;
    border-left: 3px solid var(--border-color);
}

.editor-toolbar {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1rem;
    background: var(--card-bg);
    border-radius: 8px;
}

.editor-help {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 2rem;
    border: 1px solid var(--border-color);
}

.editor-help h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.editor-help ul {
    list-style: none;
    padding-left: 0;
}

.editor-help li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
}

@media (max-width: 768px) {
    .settings-section {
        padding: 1rem;
    }
    
    .setting-item {
        padding: 1rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>