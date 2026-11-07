<?php
require_once __DIR__ . '/includes/SessionManager.php';
SessionManager::destroy();
header('Location: index.php');
exit;
