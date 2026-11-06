<?php

namespace App;

class FileUploader
{
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    private const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5MB

    /**
     * Upload an image file securely.
     *
     * @param array $file $_FILES['input_name']
     * @param string|null $oldFilename File to delete on successful replacement
     * @param string|null $errorMessage Reference to capture error string
     * @return string|null New filename on success, null on failure
     */
    public static function uploadImage(array $file, ?string $oldFilename = null, ?string &$errorMessage = null): ?string
    {
        if (empty($file['tmp_name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $errorMessage = "Tidak ada berkas yang dipilih.";
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = "Terjadi kesalahan saat mengunggah berkas (Kode: " . $file['error'] . ").";
            return null;
        }

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            $errorMessage = "Ukuran berkas melebihi batas maksimum 5MB.";
            return null;
        }

        // Validate MIME type securely using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mime, self::ALLOWED_MIMES)) {
            $errorMessage = "Format berkas tidak didukung. Harap unggah gambar JPG, PNG, atau WEBP.";
            return null;
        }

        // Validate image content
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errorMessage = "Berkas yang diunggah bukan gambar yang valid.";
            return null;
        }

        $extension = self::ALLOWED_MIMES[$mime];
        $uniqueName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $targetDir = ROOT_PATH . '/assets/images/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . $uniqueName;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errorMessage = "Gagal memindahkan berkas yang diunggah.";
            return null;
        }

        // Delete old file if provided and not a protected asset
        if ($oldFilename && !in_array($oldFilename, ['x.jpg', 'bolt.jpg', 'avatar.png', 'logo.png', 'logo-title.png', 'paw.png'], true)) {
            self::delete($oldFilename);
        }

        return $uniqueName;
    }

    /**
     * Delete an uploaded image safely.
     */
    public static function delete(string $filename): bool
    {
        $clean = basename($filename);
        $path = ROOT_PATH . '/assets/images/' . $clean;
        if (file_exists($path) && is_file($path)) {
            return unlink($path);
        }
        return false;
    }
}
