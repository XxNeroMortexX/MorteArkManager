<?php
/**
 * File Browser / FTP-like Interface
 */

define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';

// Check permission
if (!hasPermission('view_file_browser')) {
    die('Access denied');
}

// Handle file operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_file') {
        if (!hasPermission('edit_files')) {
            jsonResponse(['success' => false, 'error' => 'No permission to edit files']);
        }
        
        $filepath = $_POST['filepath'] ?? '';
        $content = $_POST['content'] ?? '';
        
        if (!isSafePath($filepath)) {
            jsonResponse(['success' => false, 'error' => 'Invalid file path']);
        }
        
        // Create backup
        if (file_exists($filepath)) {
            createBackup($filepath, 'file');
        }
        
        if (file_put_contents($filepath, $content) !== false) {
            logAction('FILE_EDIT', $filepath);
            jsonResponse(['success' => true, 'message' => 'File saved successfully']);
        } else {
            jsonResponse(['success' => false, 'error' => 'Failed to save file']);
        }
    }
    
    if ($action === 'delete_file') {
        if (!hasPermission('delete_files')) {
            jsonResponse(['success' => false, 'error' => 'No permission to delete files']);
        }
        
        $filepath = $_POST['filepath'] ?? '';
        
        if (!isSafePath($filepath)) {
            jsonResponse(['success' => false, 'error' => 'Invalid file path']);
        }
        
        if (!file_exists($filepath)) {
            jsonResponse(['success' => false, 'error' => 'File not found']);
        }
        
        // Create backup before deleting
        $backupPath = createBackup($filepath, 'deleted');
        
        if ($backupPath && unlink($filepath)) {
            logAction('FILE_DELETE', $filepath);
            jsonResponse(['success' => true, 'message' => 'File deleted (backup created)']);
        } else {
            jsonResponse(['success' => false, 'error' => 'Failed to delete file']);
        }
    }
}

// Get available roots for current user
$role = getCurrentUserRole();
$availableRoots = $FILE_BROWSER_ROOTS[$role] ?? [];

if (empty($availableRoots)) {
    die('No file browser access configured for your role');
}

// Get selected root
$selectedRoot = $_GET['root'] ?? '';
if (empty($selectedRoot) || !isset($availableRoots[$selectedRoot])) {
    $selectedRoot = array_key_first($availableRoots);
}

$rootPath = $availableRoots[$selectedRoot];

// Get current path
$currentPath = $_GET['path'] ?? '';
$fullPath = $rootPath;

if (!empty($currentPath)) {
    $requestedPath = $rootPath . DIRECTORY_SEPARATOR . ltrim($currentPath, '\\/');
    if (isSafePath($requestedPath)) {
        $fullPath = $requestedPath;
    }
}

$fullPath = realpath($fullPath);
if (!$fullPath || !isSafePath($fullPath)) {
    $fullPath = realpath($rootPath);
}

// Handle file viewing/editing
$viewFile = $_GET['view'] ?? null;
$fileContent = null;
$fileInfo = null;

if ($viewFile) {
    $viewFilePath = $fullPath . DIRECTORY_SEPARATOR . basename($viewFile);
    
    if (isSafePath($viewFilePath) && file_exists($viewFilePath) && is_file($viewFilePath)) {
        $fileInfo = [
            'name' => basename($viewFilePath),
            'path' => $viewFilePath,
            'size' => filesize($viewFilePath),
            'modified' => filemtime($viewFilePath),
            'writable' => is_writable($viewFilePath),
            'extension' => pathinfo($viewFilePath, PATHINFO_EXTENSION)
        ];
        
        if (isEditableFile($viewFilePath) && $fileInfo['size'] < 5000000) { // Max 5MB for editing
            $fileContent = file_get_contents($viewFilePath);
        }
    }
}

// Get directory contents
$items = [];
if (is_dir($fullPath)) {
    $scanResult = @scandir($fullPath);
    if ($scanResult !== false) {
        foreach ($scanResult as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $itemPath = $fullPath . DIRECTORY_SEPARATOR . $item;
            
            // Check if file should be hidden
            if (is_file($itemPath) && !isAllowedExtension($item)) {
                continue;
            }
            
            $items[] = [
                'name' => $item,
                'is_dir' => is_dir($itemPath),
                'size' => is_file($itemPath) ? filesize($itemPath) : 0,
				'modified' => (file_exists($itemPath) && is_readable($itemPath)) ? filemtime($itemPath) : null,
                'writable' => is_writable($itemPath),
                'extension' => is_file($itemPath) ? pathinfo($item, PATHINFO_EXTENSION) : ''
            ];
        }
    }
}

// Sort: directories first, then by name
usort($items, function($a, $b) {
    if ($a['is_dir'] !== $b['is_dir']) {
        return $b['is_dir'] - $a['is_dir'];
    }
    return strcasecmp($a['name'], $b['name']);
});

// Build breadcrumb
$relativePath = str_replace($rootPath, '', $fullPath);
$relativePath = trim(str_replace('\\', '/', $relativePath), '/');
$breadcrumbs = !empty($relativePath) ? explode('/', $relativePath) : [];

$pageTitle = 'File Browser';
include '../includes/header.php';
?>

<div class="page-header">
    <h1>📁 File Browser</h1>
    <p>Browse and edit server files</p>
</div>

<?php if ($fileInfo): ?>
    <!-- File Editor View -->
    <div class="file-editor-container">
        <div class="editor-header">
            <div>
                <a href="?root=<?php echo urlencode($selectedRoot); ?>&path=<?php echo urlencode($relativePath); ?>" class="btn btn-secondary">
                    ← Back to Files
                </a>
            </div>
            <h2>📄 <?php echo htmlspecialchars($fileInfo['name']); ?></h2>
        </div>

        <div class="file-info-bar">
            <span><strong>Size:</strong> <?php echo formatFileSize($fileInfo['size']); ?></span>
            <span><strong>Modified:</strong> <?php echo date('Y-m-d H:i:s', $fileInfo['modified']); ?></span>
            <span><strong>Writable:</strong> <?php echo $fileInfo['writable'] ? '✅' : '❌'; ?></span>
        </div>

        <?php if ($fileContent !== null && hasPermission('edit_files')): ?>
            <form id="fileEditForm" class="file-edit-form">
                <input type="hidden" name="action" value="save_file">
                <input type="hidden" name="filepath" value="<?php echo htmlspecialchars($fileInfo['path']); ?>">
                
                <div class="editor-toolbar">
                    <button type="submit" class="btn btn-success">💾 Save File</button>
                    <button type="button" class="btn btn-secondary" onclick="resetEditor()">↺ Reset</button>
                    <?php if (hasPermission('delete_files')): ?>
                    <button type="button" class="btn btn-danger" onclick="deleteFile()">🗑️ Delete</button>
                    <?php endif; ?>
                </div>

                <textarea name="content" id="fileEditor" class="code-editor" rows="30"><?php echo htmlspecialchars($fileContent); ?></textarea>
            </form>
        <?php elseif ($fileContent !== null): ?>
            <div class="code-editor" style="background: var(--darker-bg); padding: 1rem; overflow: auto; max-height: 600px;">
                <pre style="margin: 0; color: white;"><?php echo htmlspecialchars($fileContent); ?></pre>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                ⚠️ File is too large, not editable, or you don't have permission to edit.
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- File Browser View -->
    <div class="file-browser-container">
        <div class="file-browser-controls">
            <div class="root-selector">
                <label>Directory:</label>
                <select class="form-control" onchange="window.location='?root='+this.value" style="display: inline-block; width: auto;">
                    <?php foreach ($availableRoots as $key => $path): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $selectedRoot === $key ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($key); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="path-input">
                <form method="GET" style="display: flex; gap: 0.5rem;">
                    <input type="hidden" name="root" value="<?php echo htmlspecialchars($selectedRoot); ?>">
                    <input type="text" 
                           name="path" 
                           value="<?php echo htmlspecialchars($currentPath); ?>" 
                           placeholder="Enter path relative to root..."
                           class="form-control">
                    <button type="submit" class="btn btn-primary">Go</button>
                </form>
            </div>
        </div>
        
        <div class="breadcrumb">
            <a href="?root=<?php echo urlencode($selectedRoot); ?>">📁 <?php echo htmlspecialchars($selectedRoot); ?></a>
            <?php
            $pathSoFar = '';
            foreach ($breadcrumbs as $crumb):
                $pathSoFar .= ($pathSoFar ? '/' : '') . $crumb;
            ?>
                <span class="separator">/</span>
                <a href="?root=<?php echo urlencode($selectedRoot); ?>&path=<?php echo urlencode($pathSoFar); ?>">
                    <?php echo htmlspecialchars($crumb); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="current-path">
            <strong>Current Directory:</strong> <?php echo htmlspecialchars($fullPath); ?>
        </div>

        <div class="files-list">
            <table class="files-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($breadcrumbs)): ?>
                        <tr class="parent-dir">
                            <td colspan="4">
                                <?php
                                $parentPath = dirname($relativePath);
                                if ($parentPath === '.' || $parentPath === '\\' || $parentPath === '/') $parentPath = '';
                                ?>
                                <a href="?root=<?php echo urlencode($selectedRoot); ?>&path=<?php echo urlencode($parentPath); ?>" style="color: white;">
                                    📁 .. (Parent Directory)
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="4" class="empty-dir">Empty directory</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr class="<?php echo $item['is_dir'] ? 'directory-row' : 'file-row'; ?>">
                                <td class="name-cell">
                                    <?php if ($item['is_dir']): ?>
                                        <a href="?root=<?php echo urlencode($selectedRoot); ?>&path=<?php echo urlencode($relativePath . ($relativePath ? '/' : '') . $item['name']); ?>" class="dir-link" style="color: white;">
                                            📁 <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="file-icon">
                                            <?php
                                            $icon = match(strtolower($item['extension'])) {
                                                'ini', 'cfg' => '⚙️',
                                                'txt', 'log' => '📄',
                                                'json' => '📋',
                                                'bat', 'sh' => '📜',
                                                'py' => '🐍',
                                                default => '📄'
                                            };
                                            echo $icon;
                                            ?>
                                        </span>
                                        <span style="color: white;"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="size-cell">
                                    <?php echo $item['is_dir'] ? '-' : formatFileSize($item['size']); ?>
                                </td>
                                <td class="date-cell">
                                    <?php echo date('Y-m-d H:i', $item['modified']); ?>
                                </td>
                                <td class="actions-cell">
                                    <?php if (!$item['is_dir'] && isEditableFile($item['name'])): ?>
                                        <a href="?root=<?php echo urlencode($selectedRoot); ?>&path=<?php echo urlencode($relativePath); ?>&view=<?php echo urlencode($item['name']); ?>" 
                                           class="btn btn-sm btn-primary">
                                            <?php echo hasPermission('edit_files') ? '✏️ Edit' : '👁️ View'; ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
<?php if ($fileInfo && $fileContent !== null && hasPermission('edit_files')): ?>
const originalContent = document.getElementById('fileEditor').value;

function resetEditor() {
    if (confirm('Reset to original content?')) {
        document.getElementById('fileEditor').value = originalContent;
    }
}

document.getElementById('fileEditForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ ' + result.message);
            location.reload();
        } else {
            alert('❌ ' + result.error);
        }
    } catch (error) {
        alert('❌ Request failed: ' + error.message);
    }
});

async function deleteFile() {
    if (!confirm('Delete this file? A backup will be created.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_file');
    formData.append('filepath', '<?php echo addslashes($fileInfo['path']); ?>');
    
    try {
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ ' + result.message);
            window.location = '?root=<?php echo urlencode($selectedRoot); ?>&path=<?php echo urlencode($relativePath); ?>';
        } else {
            alert('❌ ' + result.error);
        }
    } catch (error) {
        alert('❌ Request failed: ' + error.message);
    }
}

// Warn before leaving with unsaved changes
window.addEventListener('beforeunload', function(e) {
    const current = document.getElementById('fileEditor').value;
    if (current !== originalContent) {
        e.preventDefault();
        e.returnValue = '';
    }
});
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>