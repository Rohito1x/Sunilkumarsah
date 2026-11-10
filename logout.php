<?php
require_once __DIR__ . '/includes/SessionManager.php';
SessionManager::destroy();
if (!headers_sent()) {
    header('Cache-Control: private, no-cache, no-store, must-revalidate');
}
header('Location: /login.php?logout=1');
exit;
