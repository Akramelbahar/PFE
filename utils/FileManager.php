<?php
require_once 'utils/Utils.php';

/**
 * FileManager class for handling file operations
 */
class FileManager {
    private $uploadDirectory;
    private $allowedExtensions;
    private $maxFileSize;

    /**
     * Constructor
     * @param string $uploadDirectory
     * @param array $allowedExtensions
     * @param int $maxFileSize Maximum file size in bytes (default 5MB)
     */
    public function __construct($uploadDirectory = 'uploads/', $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'], $maxFileSize = 5242880) {
        $this->uploadDirectory = $uploadDirectory;
        $this->allowedExtensions = $allowedExtensions;
        $this->maxFileSize = $maxFileSize;

        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0755, true);
        }
    }

    /**
     * Upload a file
     * @param array $file File from $_FILES array
     * @param string $customDir Subdirectory for the file (optional)
     * @return array|bool Array with file info on success, false on failure
     */
    public function upload($file, $customDir = '') {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }

        // Check for errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return false;
        }

        // Check file extension
        $extension = Utils::getFileExtension($file['name']);
        if (!in_array($extension, $this->allowedExtensions)) {
            return false;
        }

        // Generate unique filename
        $filename = Utils::uniqueFilename($file['name']);

        // Set upload path
        $uploadPath = $this->uploadDirectory;
        if (!empty($customDir)) {
            $uploadPath .= $customDir . '/';
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
        }

        $fullPath = $uploadPath . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return [
                'filename' => $filename,
                'path' => $fullPath,
                'url' => $uploadPath . $filename,
                'extension' => $extension,
                'size' => $file['size'],
                'mime' => $file['type']
            ];
        }

        return false;
    }

    /**
     * Upload multiple files
     * @param array $files Files from $_FILES array
     * @param string $customDir Subdirectory for the files (optional)
     * @return array Array of upload results
     */
    public function uploadMultiple($files, $customDir = '') {
        $uploads = [];

        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];

                $result = $this->upload($file, $customDir);
                if ($result) {
                    $uploads[] = $result;
                }
            }
        }

        return $uploads;
    }

    /**
     * Delete a file
     * @param string $filename
     * @param string $customDir Subdirectory for the file (optional)
     * @return bool
     */
    public function delete($filename, $customDir = '') {
        $path = $this->uploadDirectory;
        if (!empty($customDir)) {
            $path .= $customDir . '/';
        }
        $path .= $filename;

        if (file_exists($path)) {
            return unlink($path);
        }

        return false;
    }

    /**
     * Get file info
     * @param string $filename
     * @param string $customDir Subdirectory for the file (optional)
     * @return array|bool
     */
    public function getFileInfo($filename, $customDir = '') {
        $path = $this->uploadDirectory;
        if (!empty($customDir)) {
            $path .= $customDir . '/';
        }
        $path .= $filename;

        if (file_exists($path)) {
            $fileInfo = [
                'filename' => $filename,
                'path' => $path,
                'url' => $path,
                'extension' => Utils::getFileExtension($filename),
                'size' => filesize($path),
                'mime' => mime_content_type($path),
                'created' => filectime($path),
                'modified' => filemtime($path)
            ];

            return $fileInfo;
        }

        return false;
    }

    /**
     * Check if a file exists
     * @param string $filename
     * @param string $customDir Subdirectory for the file (optional)
     * @return bool
     */
    public function exists($filename, $customDir = '') {
        $path = $this->uploadDirectory;
        if (!empty($customDir)) {
            $path .= $customDir . '/';
        }
        $path .= $filename;

        return file_exists($path);
    }

    /**
     * Get all files in a directory
     * @param string $customDir Subdirectory to list (optional)
     * @return array
     */
    public function listFiles($customDir = '') {
        $path = $this->uploadDirectory;
        if (!empty($customDir)) {
            $path .= $customDir . '/';
        }

        $files = [];

        if (file_exists($path) && is_dir($path)) {
            $dirContents = scandir($path);

            foreach ($dirContents as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                if (is_file($path . $item)) {
                    $files[] = $this->getFileInfo($item, $customDir);
                }
            }
        }

        return $files;
    }

    /**
     * Create a zip archive of multiple files
     * @param array $files Array of filenames
     * @param string $zipFilename Name for the zip file
     * @param string $customDir Subdirectory for the files (optional)
     * @return string|bool Path to zip file on success, false on failure
     */
    public function createZip($files, $zipFilename, $customDir = '') {
        $zip = new ZipArchive();

        $path = $this->uploadDirectory;
        if (!empty($customDir)) {
            $path .= $customDir . '/';
        }

        $zipPath = $this->uploadDirectory . $zipFilename;

        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($files as $file) {
                if (file_exists($path . $file)) {
                    $zip->addFile($path . $file, $file);
                }
            }

            $zip->close();
            return $zipPath;
        }

        return false;
    }
    // In your FileManager class
    public function download($filename, $customDir = '') {
        $path = $this->uploadDirectory;
        if (!empty($customDir)) {
            $path .= $customDir . '/';
        }
        $filePath = $path . $filename;

        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            return false;
        }
    }
}