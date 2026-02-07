<?php

namespace App\Helpers;

class FileUploadHelper
{
    private static $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif'];
    private static $allowedDocumentTypes = ['pdf', 'doc', 'docx'];
    private static $maxFileSize = 5242880; // Increased limit to 5MB (we will resize it down)
    
    /**
     * Upload profile photo with optimization
     */
    public static function uploadProfilePhoto($file, $userId)
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('No file uploaded or upload error occurred');
        }
        
        // Validate file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, self::$allowedImageTypes)) {
            throw new \Exception('Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');
        }
        
        // Validate file size (hard limit)
        if ($file['size'] > self::$maxFileSize) {
            throw new \Exception('File size too large. Maximum upload size is 5MB.');
        }
        
        // Validate image data
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new \Exception('Invalid image file.');
        }
        
        // Create upload directory
        $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
        $filepath = $uploadDir . $filename;
        
        // Optimize and Save
        // We resize to max 800x800 which is plenty for profile pictures and ID cards
        if (self::resizeAndSaveImage($file['tmp_name'], $filepath, 800, 800, 85)) {
            return 'uploads/profiles/' . $filename;
        }
        
        // Fallback: Just move the file if optimization fails
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Failed to save uploaded file.');
        }
        
        return 'uploads/profiles/' . $filename;
    }
    
    /**
     * Resize and save image using GD library
     */
    private static function resizeAndSaveImage($sourcePath, $destPath, $maxWidth, $maxHeight, $quality = 85)
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        list($origWidth, $origHeight, $type) = getimagesize($sourcePath);

        // Load image based on type
        switch ($type) {
            case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($sourcePath); break;
            case IMAGETYPE_PNG:  $image = imagecreatefrompng($sourcePath); break;
            case IMAGETYPE_GIF:  $image = imagecreatefromgif($sourcePath); break;
            default: return false;
        }

        if (!$image) return false;

        // Calculate new dimensions keeping aspect ratio
        $ratio = $origWidth / $origHeight;
        if ($maxWidth / $maxHeight > $ratio) {
            $newWidth = $maxHeight * $ratio;
            $newHeight = $maxHeight;
        } else {
            $newHeight = $maxWidth / $ratio;
            $newWidth = $maxWidth;
        }

        // Resample
        $newImage = imagecreatetruecolor((int)$newWidth, (int)$newHeight);

        // Preserve transparency for PNG/GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, (int)$newWidth, (int)$newHeight, $origWidth, $origHeight);

        // Save
        $result = false;
        $ext = strtolower(pathinfo($destPath, PATHINFO_EXTENSION));
        
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $result = imagejpeg($newImage, $destPath, $quality);
                break;
            case 'png':
                // PNG quality is 0-9 (0 is no compression, 9 is max)
                $pngQuality = (int)(9 * (1 - ($quality / 100)));
                $result = imagepng($newImage, $destPath, $pngQuality);
                break;
            case 'gif':
                $result = imagegif($newImage, $destPath);
                break;
        }

        imagedestroy($image);
        imagedestroy($newImage);

        return $result;
    }
    
    /**
     * Upload document (CV, certificates, etc.) - No resizing for docs
     */
    public static function uploadDocument($file, $userId, $type = 'document')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('No file uploaded or upload error occurred');
        }
        
        // Validate file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedTypes = array_merge(self::$allowedImageTypes, self::$allowedDocumentTypes);
        
        if (!in_array($fileExtension, $allowedTypes)) {
            throw new \Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedTypes));
        }
        
        // Validate file size
        if ($file['size'] > self::$maxFileSize) {
            throw new \Exception('File size too large. Maximum size is 5MB.');
        }
        
        // Create upload directory
        $uploadDir = __DIR__ . '/../../public/uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = $type . '_' . $userId . '_' . time() . '.' . $fileExtension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Failed to save uploaded file.');
        }
        
        return 'uploads/documents/' . $filename;
    }
    
    /**
     * Delete uploaded file
     */
    public static function deleteFile($relativePath)
    {
        if (empty($relativePath)) {
            return true;
        }
        
        $fullPath = __DIR__ . '/../../public/' . $relativePath;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return true;
    }
    
    /**
     * Get file size in human readable format
     */
    public static function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Validate uploaded file
     */
    public static function validateFile($file, $type = 'image')
    {
        $errors = [];
        
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'No file uploaded or upload error occurred';
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > self::$maxFileSize) {
            $errors[] = 'File size too large. Maximum size is ' . self::formatFileSize(self::$maxFileSize);
        }
        
        // Check file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($type === 'image') {
            if (!in_array($fileExtension, self::$allowedImageTypes)) {
                $errors[] = 'Invalid file type. Only ' . implode(', ', self::$allowedImageTypes) . ' files are allowed.';
            }
        } else {
            $allowedTypes = array_merge(self::$allowedImageTypes, self::$allowedDocumentTypes);
            if (!in_array($fileExtension, $allowedTypes)) {
                $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes);
            }
        }
        
        return $errors;
    }
}