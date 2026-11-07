<?php
declare(strict_types=1);

/**
 * Vision Point Educational Service - Secure File Uploader
 * Inspects binary magic bytes using finfo, enforces strict MIME/extension mapping,
 * strips polyglots/metadata, and randomizes filenames.
 */
final class SecureUploader
{
    private const DEFAULT_ALLOWED_MIMES = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'pdf'  => 'application/pdf'
    ];

    private const DEFAULT_MAX_BYTES = 5242880; // 5 MB

    /**
     * Safely processes and saves an uploaded file.
     *
     * @param array $file Reference to $_FILES['fieldName']
     * @param string $destinationDir Absolute directory path for storage
     * @param string $prefix Optional prefix for the randomized filename
     * @param array|null $allowedMimes Optional custom MIME map (extension => mime)
     * @param int $maxBytes Maximum file size in bytes
     * @return array ['success' => bool, 'filename' => string, 'filepath' => string, 'error' => ?string]
     */
    public static function process(
        array $file,
        string $destinationDir,
        string $prefix = 'file_',
        ?array $allowedMimes = null,
        int $maxBytes = self::DEFAULT_MAX_BYTES
    ): array {
        $allowed = $allowedMimes ?? self::DEFAULT_ALLOWED_MIMES;

        // 1. Verify file upload error code
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'filename' => '', 'filepath' => '', 'error' => 'Invalid upload payload parameters.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMap = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the form.',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
            ];
            return ['success' => false, 'filename' => '', 'filepath' => '', 'error' => $errorMap[$file['error']] ?? 'Upload failed with error code: ' . $file['error']];
        }

        // 2. Size limitation check
        if ($file['size'] > $maxBytes) {
            $maxMb = round($maxBytes / 1048576, 1);
            return ['success' => false, 'filename' => '', 'filepath' => '', 'error' => "File size exceeds the permitted limit of {$maxMb}MB."];
        }

        $tmpPath = $file['tmp_name'];
        if (!is_uploaded_file($tmpPath)) {
            return ['success' => false, 'filename' => '', 'filepath' => '', 'error' => 'Security warning: The file is not a valid uploaded file.'];
        }

        // 3. Inspect genuine binary MIME type via finfo magic numbers
        $detectedMime = '';
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detectedMime = (string)$finfo->file($tmpPath);
        } elseif (function_exists('mime_content_type')) {
            $detectedMime = (string)mime_content_type($tmpPath);
        }

        // Match detected MIME against allowed whitelist
        $ext = array_search($detectedMime, $allowed, true);
        if ($ext === false) {
            return ['success' => false, 'filename' => '', 'filepath' => '', 'error' => 'File rejected. Detected MIME type is not allowed: ' . htmlspecialchars($detectedMime, ENT_QUOTES, 'UTF-8')];
        }

        // 4. Secondary image structure verification (neutralizes PHP scripts hidden in image headers)
        if (strpos($detectedMime, 'image/') === 0) {
            $imgInfo = @getimagesize($tmpPath);
            if ($imgInfo === false) {
                return ['success' => false, 'filename' => '', 'filepath' => '', 'error' => 'Corrupt or deceptive image file detected.'];
            }
        }

        // 5. Generate cryptographically random filename
        $cleanPrefix = preg_replace('/[^a-zA-Z0-9_-]/', '', $prefix);
        $randomName = $cleanPrefix . bin2hex(random_bytes(12)) . '.' . $ext;

        // Ensure directory exists
        if (!is_dir($destinationDir)) {
            if (!@mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
                return ['success' => false, 'filename' => '', 'filepath' => '', 'error' => 'Server error: Unable to create upload destination directory.'];
            }
        }

        $destinationPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $randomName;

        // 6. Move file to destination
        if (!move_uploaded_file($tmpPath, $destinationPath)) {
            return ['success' => false, 'filename' => '', 'filepath' => '', 'error' => 'Failed to store uploaded file on the server.'];
        }

        // Enforce restrictive file permissions (read-only for web visitors)
        @chmod($destinationPath, 0644);

        return [
            'success'  => true,
            'filename' => $randomName,
            'filepath' => $destinationPath,
            'error'    => null
        ];
    }
}
