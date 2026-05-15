<?php
/**
 * Storage file server — serves files from storage/app/public/
 * Used as fallback when Apache cannot follow the public/storage symlink.
 */

// Get requested path from URI  /storage/gallery/image.jpg → gallery/image.jpg
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!preg_match('#^/storage/(.+)$#', $uri, $m)) {
    http_response_code(404); exit;
}

$relative = $m[1];

// Security: block directory traversal
if (str_contains($relative, '..') || str_contains($relative, "\0")) {
    http_response_code(403); exit;
}

$storagePath = dirname(__DIR__) . '/storage/app/public/' . $relative;

if (!is_file($storagePath)) {
    http_response_code(404); exit;
}

$mime = mime_content_type($storagePath) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($storagePath));
header('Cache-Control: public, max-age=31536000');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($storagePath)) . ' GMT');
readfile($storagePath);
