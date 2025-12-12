<?php
/**
 * Security helper functions
 * Provides XSS protection, input validation, and other security utilities
 */

/**
 * Escape output for HTML context (XSS protection)
 */
function e(?string $str): string {
    if ($str === null) {
        return '';
    }
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Escape output for use in HTML attributes
 */
function eAttr(?string $str): string {
    return e($str);
}

/**
 * Escape output for use in JavaScript
 * Uses JSON encoding with safe flags, handles invalid UTF-8 gracefully
 */
function eJs(?string $str): string {
    if ($str === null) {
        return '""';
    }

    try {
        $result = json_encode($str, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_THROW_ON_ERROR);
        return $result;
    } catch (JsonException $e) {
        // Invalid UTF-8: attempt to convert to valid UTF-8 and retry
        $cleanStr = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        try {
            return json_encode($cleanStr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // Final fallback: return empty string literal
            return '""';
        }
    }
}

/**
 * Validate and sanitize integer input
 */
function sanitizeInt($value, int $default = 0): int {
    $filtered = filter_var($value, FILTER_VALIDATE_INT);
    return $filtered !== false ? $filtered : $default;
}

/**
 * Validate and sanitize positive integer
 */
function sanitizePositiveInt($value, int $default = 0): int {
    $int = sanitizeInt($value, $default);
    return $int > 0 ? $int : $default;
}

/**
 * Validate and sanitize float input
 */
function sanitizeFloat($value, float $default = 0.0): float {
    $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);
    return $filtered !== false ? $filtered : $default;
}

/**
 * Sanitize string input (trim and remove null bytes)
 */
function sanitizeString(?string $str): string {
    if ($str === null) {
        return '';
    }
    return trim(str_replace("\0", '', $str));
}

/**
 * Validate username (alphanumeric, 3-40 chars)
 */
function validateUsername(string $username): bool {
    return preg_match('/^[a-zA-Z0-9_]{3,40}$/', $username) === 1;
}

/**
 * Validate phone number (basic validation)
 */
function validatePhoneNumber(string $phone): bool {
    // Allow digits, spaces, dashes, plus sign, parentheses
    $cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone);
    return preg_match('/^\+?[0-9]{8,15}$/', $cleaned) === 1;
}

/**
 * Validate password strength
 */
function validatePassword(string $password): array {
    $errors = [];

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }

    return $errors;
}

/**
 * Hash password using bcrypt
 */
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password against hash (supports MD5 migration)
 * Returns: 'valid' | 'invalid' | 'needs_upgrade'
 */
function verifyPassword(string $password, string $hash): string {
    // Check if it's an MD5 hash (32 hex chars)
    if (preg_match('/^[a-f0-9]{32}$/i', $hash)) {
        if (md5($password) === $hash) {
            return 'needs_upgrade';
        }
        return 'invalid';
    }

    // BCrypt verification
    if (password_verify($password, $hash)) {
        // Check if rehash needed
        if (password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12])) {
            return 'needs_upgrade';
        }
        return 'valid';
    }

    return 'invalid';
}

/**
 * Generate a secure random token
 */
function generateToken(int $length = 32): string {
    return bin2hex(random_bytes($length));
}

/**
 * Constant-time string comparison
 */
function secureCompare(string $known, string $user): bool {
    return hash_equals($known, $user);
}

/**
 * File upload validation
 */
function validateImageUpload(array $file, int $maxSize = 5242880): array {
    $errors = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload gagal: ' . getUploadErrorMessage($file['error']);
        return $errors;
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        $errors[] = 'Ukuran file terlalu besar (maksimal ' . ($maxSize / 1024 / 1024) . 'MB)';
    }

    // Check MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = 'Tipe file tidak diizinkan';
    }

    // Check extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        $errors[] = 'Ekstensi file tidak diizinkan';
    }

    // Verify it's actually an image
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = 'File bukan gambar yang valid';
    }

    return $errors;
}

/**
 * Get human-readable upload error message
 */
function getUploadErrorMessage(int $error): string {
    return match($error) {
        UPLOAD_ERR_INI_SIZE => 'File terlalu besar',
        UPLOAD_ERR_FORM_SIZE => 'File terlalu besar',
        UPLOAD_ERR_PARTIAL => 'Upload tidak lengkap',
        UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih',
        UPLOAD_ERR_NO_TMP_DIR => 'Server error: tmp folder missing',
        UPLOAD_ERR_CANT_WRITE => 'Server error: cannot write',
        UPLOAD_ERR_EXTENSION => 'Upload diblokir oleh server',
        default => 'Unknown error'
    };
}

/**
 * Generate safe filename for uploads
 */
function generateSafeFilename(string $originalName): string {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $safeName = bin2hex(random_bytes(16));
    return $safeName . '.' . $extension;
}

/**
 * Set security headers
 */
function setSecurityHeaders(): void {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');

    // Prevent MIME sniffing
    header('X-Content-Type-Options: nosniff');

    // XSS protection (legacy browsers)
    header('X-XSS-Protection: 1; mode=block');

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Content Security Policy (basic - adjust as needed)
    // Note: Using 'unsafe-inline' for scripts/styles due to inline code in legacy codebase
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://kit.fontawesome.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://ka-f.fontawesome.com; img-src 'self' data:; connect-src 'self' https://ka-f.fontawesome.com;");
}
