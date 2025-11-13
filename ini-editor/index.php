<?php
/**
 * INI File Editor
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

// Handle file save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $file = $_POST['file'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (!isset($allowedInis[$file])) {
        $_SESSION['error'] = 'You do not have permission to edit this file';
        redirect('?file=' . urlencode($file));
    }
    
    // Check specific permission
    $iniKey = strtolower(str_replace('.ini', '', $file));
    $permission = 'edit_ini_' . $iniKey;
    
    if (!hasPermission('all') && !hasPermission($permission)) {
        $_SESSION['error'] = 'You do not have permission to edit this file';
        redirect('?file=' . urlencode($file));
    }
    
    $filepath = $allowedInis[$file];
    
    // If limited editing, only allow specific keys
    if (hasPermission('edit_ini_limited') && !hasPermission('all')) {
        global $INI_EDITABLE_KEYS;
        $role = getCurrentUserRole();
        $allowedKeys = $INI_EDITABLE_KEYS[$role][$file] ?? [];
        
        if (!empty($allowedKeys)) {
            // Parse both old and new content
            $oldContent = file_get_contents($filepath);
            $oldParsed = parse_ini_string($oldContent, true);
            $newParsed = parse_ini_string($content, true);
            
            // Only allow changes to specific keys
            foreach ($newParsed as $section => $values) {
                if (!is_array($values)) continue;
                
                foreach ($values as $key => $value) {
                    if (!in_array($key, $allowedKeys)) {
                        // Revert to original value
                        if (isset($oldParsed[$section][$key])) {
                            $newParsed[$section][$key] = $oldParsed[$section][$key];
                        }
                    }
                }
            }
            
            // Reconstruct INI content
            $content = '';
            foreach ($newParsed as $section => $values) {
                if (is_array($values)) {
                    $content .= "[$section]\n";
                    foreach ($values as $key => $value) {
                        $content .= "$key=$value\n";
                    }
                    $content .= "\n";
                }
            }
        }
    }
    
    // Create backup
    createBackup($filepath, 'ini');
    
    // Save new content
    if (file_put_contents($filepath, $content) !== false) {
        logAction('INI_EDIT', "Edited $file");
        $_SESSION['success'] = "Successfully saved $file (backup created in " . BACKUP_DIR . ")";
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
$content = file_exists($filepath) ? file_get_contents($filepath) : '';
$fileInfo = file_exists($filepath) ? [
    'size' => filesize($filepath),
    'modified' => filemtime($filepath),
    'writable' => is_writable($filepath)
] : null;

// Check if user can edit this file
$iniKey = strtolower(str_replace('.ini', '', $selectedFile));
$permission = 'edit_ini_' . $iniKey;
$canEdit = hasPermission('all') || hasPermission($permission);

// Get editable keys if limited editing
$editableKeysInfo = null;
if (hasPermission('edit_ini_limited') && !hasPermission('all')) {
    global $INI_EDITABLE_KEYS;
    $role = getCurrentUserRole();
    $editableKeysInfo = $INI_EDITABLE_KEYS[$role][$selectedFile] ?? null;
}

$pageTitle = 'INI Editor';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>📝 INI File Editor</h1>
    <p>Edit server configuration files</p>
</div>

<div class="ini-editor-container">
    <div class="file-selector">
        <h3>Select INI File:</h3>
        <div class="file-tabs">
            <?php foreach ($allowedInis as $name => $path): ?>
                <a href="?file=<?php echo urlencode($name); ?>" 
                   class="file-tab <?php echo $selectedFile === $name ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($fileInfo): ?>
        <div class="file-info">
            <span><strong>File:</strong> <?php echo htmlspecialchars($selectedFile); ?></span>
            <span><strong>Size:</strong> <?php echo formatFileSize($fileInfo['size']); ?></span>
            <span><strong>Modified:</strong> <?php echo date('Y-m-d H:i:s', $fileInfo['modified']); ?></span>
            <span><strong>Writable:</strong> <?php echo $fileInfo['writable'] ? '✅ Yes' : '❌ No'; ?></span>
            <?php if (!$canEdit): ?>
            <span style="color: var(--warning-color);"><strong>⚠️ Read-Only</strong></span>
            <?php endif; ?>
        </div>

        <?php if ($editableKeysInfo): ?>
            <div class="alert alert-info">
                ℹ️ You can only edit these keys: <?php echo implode(', ', $editableKeysInfo); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="editor-form">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="file" value="<?php echo htmlspecialchars($selectedFile); ?>">
            
            <div class="editor-toolbar">
                <?php if ($canEdit): ?>
                <button type="submit" class="btn btn-success">💾 Save Changes</button>
                <button type="button" class="btn btn-secondary" onclick="resetContent()">↺ Reset</button>
                <?php endif; ?>
                <a href="backup.php?file=<?php echo urlencode($selectedFile); ?>" class="btn btn-info">
                    📦 View Backups
                </a>
            </div>

            <textarea name="content" 
                      id="editor" 
                      class="ini-editor" 
                      rows="30"
                      <?php echo !$canEdit ? 'readonly' : ''; ?>><?php echo htmlspecialchars($content); ?></textarea>
        </form>

        <div class="editor-help">
            <h3>💡 Quick Help</h3>
            <ul>
                <li><strong>GameUserSettings.ini</strong> - Server settings, rates, passwords, multipliers</li>
                <li><strong>Game.ini</strong> - Advanced gameplay settings, engrams, loot, harvesting</li>
                <li><strong>Engine.ini</strong> - Engine-level optimizations and performance</li>
                <li>⚠️ Changes require server restart to take effect</li>
                <li>✅ Backups are automatically created in: <?php echo htmlspecialchars(BACKUP_DIR); ?></li>
                <?php if ($editableKeysInfo): ?>
                <li>🔒 You have limited editing rights - only specific keys can be changed</li>
                <?php endif; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="alert alert-error">
            <strong>Error:</strong> File not found at <?php echo htmlspecialchars($filepath); ?>
        </div>
    <?php endif; ?>
</div>

<script>
const originalContent = document.getElementById('editor').value;
const canEdit = <?php echo $canEdit ? 'true' : 'false'; ?>;

function resetContent() {
    if (confirm('Reset to original content? Unsaved changes will be lost.')) {
        document.getElementById('editor').value = originalContent;
    }
}

// Warn before leaving with unsaved changes
if (canEdit) {
    window.addEventListener('beforeunload', function(e) {
        const currentContent = document.getElementById('editor').value;
        if (currentContent !== originalContent) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>