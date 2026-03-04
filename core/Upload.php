<?php
namespace Caracal\Core;

class Upload
{
    protected Storage $storage;
    protected int $maxSize;
    protected array $allowedMime;
    protected array $allowedExtensions;

    public function __construct(array $config = [])
    {
        $this->storage = new Storage($config['path'] ?? null);

        $this->maxSize = isset($config['max_size'])
            ? $config['max_size']
            : $this->parseSize(Helpers::env('UPLOAD_MAX_SIZE', '5M'));

        $this->allowedMime = $config['allowed_mime'] ?? [];
        $this->allowedExtensions = $config['allowed_ext'] ?? [];
    }

    public function save(array $file, string $targetPath, bool $overwrite = false): array
    {
        $files = $this->normalizeFiles($file);
        $results = [];

        foreach ($files as $f) {
            $results[] = $this->saveSingle($f, $targetPath, $overwrite);
        }

        return $results;
    }

    protected function saveSingle(array $file, string $targetPath, bool $overwrite): array
    {
        if (!isset($file['tmp_name'], $file['name'], $file['size'])) {
            throw new \Exception("Invalid uploaded file");
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception($this->fileUploadError($file['error']));
        }

        if ($file['size'] > $this->maxSize) {
            throw new \Exception("File exceeds maximum upload size of {$this->maxSize} bytes");
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!empty($this->allowedExtensions) && !in_array($ext, $this->allowedExtensions)) {
            throw new \Exception("Extension .{$ext} not allowed");
        }

        if (!empty($this->allowedMime)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $this->allowedMime)) {
                throw new \Exception("File type {$mime} not allowed");
            }
        }

        $filename = basename($file['name']);
        $savePath = rtrim($targetPath, '/') . '/' . $filename;

        if (!$overwrite && $this->storage->exists($savePath)) {
            $filename = $this->generateUniqueFilename($targetPath, $filename);
            $savePath = rtrim($targetPath, '/') . '/' . $filename;
        }

        $content = file_get_contents($file['tmp_name']);
        if ($content === false) {
            throw new \Exception("Failed to read uploaded file");
        }

        if (!$this->storage->put($savePath, $content)) {
            throw new \Exception("Failed to save file to storage");
        }

        return [
            'original_name' => $file['name'],
            'stored_name'   => $filename,
            'path'          => $savePath,
            'size'          => $file['size'],
            'extension'     => $ext,
        ];
    }

    protected function normalizeFiles(array $file): array
    {
        if (!isset($file['name'])) {
            return [];
        }

        if (is_array($file['name'])) {
            $files = [];
            foreach ($file['name'] as $i => $name) {
                $files[] = [
                    'name' => $name,
                    'type' => $file['type'][$i],
                    'tmp_name' => $file['tmp_name'][$i],
                    'error' => $file['error'][$i],
                    'size' => $file['size'][$i],
                ];
            }
            return $files;
        }

        return [$file];
    }

    protected function generateUniqueFilename(string $path, string $filename): string
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $i = 1;

        while ($this->storage->exists(rtrim($path, '/') . '/' . $filename)) {
            $filename = "{$name}_{$i}.{$ext}";
            $i++;
        }

        return $filename;
    }

    protected function parseSize(string $size): int
    {
        $unit = strtoupper(substr($size, -1));
        $num = (int) $size;

        return match($unit) {
            'K' => $num * 1024,
            'M' => $num * 1024 * 1024,
            'G' => $num * 1024 * 1024 * 1024,
            default => $num,
        };
    }

    protected function fileUploadError(int $code): string
    {
        return match($code) {
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
            UPLOAD_ERR_NO_FILE => "No file was uploaded",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
            UPLOAD_ERR_EXTENSION => "File upload stopped by extension",
            default => "Unknown upload error",
        };
    }
}