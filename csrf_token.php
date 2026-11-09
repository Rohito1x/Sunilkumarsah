<?php
/**
 * Vision Point Educational Service - CSRF Token Refresh Endpoint
 * Returns a fresh CSRF token as JSON for AJAX-based form submissions.
 * Called by the frontend JS before each form submission to ensure token freshness.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once __DIR__ . '/includes/db.php';

// Ensure a valid CSRF token exists in session
$token = Csrf::getToken();

echo json_encode([
    'success' => true,
    'token'   => $token
]);
