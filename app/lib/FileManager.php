<?php

namespace App\Lib;

use App\Constants\FileInfo;
use Intervention\Image\Laravel\Facades\Image;

class FileManager
{
    /*
    |--------------------------------------------------------------------------
    | File Manager
    |--------------------------------------------------------------------------
    |
    | FileManager class is used to manage edit, update, remove files. Developer
    | can manage any kind of files from here. But some limitations are here for images.
    | This class uses a trait to manage the file paths and sizes. Developer can also
    | use this class as a helper function.
    |
    */

    /**
     * The file which will be uploaded
     *
     * @var object
     */
    protected $file;

    /**
     * The path where the file will be uploaded
     *
     * @var string
     */
    public $path;

    /**
     * The size, if the file is an image
     *
     * @var string
     */
    public $size;

    /**
     * Check if the file is an image or not
     *
     * @var boolean
     */
    protected $isImage;

    /**
     * Thumbnail version size, if required and if the file is an image
     *
     * @var string
     */
    public $thumb;

    /**
     * Old filename, which will be removed
     *
     * @var string
     */
    public $old;

    /**
     * Current filename, which is uploading
     *
     * @var string
     */
    public $filename;

    /**
     * Set the file and file type to properties if it exists
     *
     * @param $file
     * @return void
     */
    public function __construct($file = null)
    {
        $this->file = $file;
        if ($file) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];
            if (in_array($file->getClientOriginalExtension(), $imageExtensions)) {
                $this->isImage = true;
            } else {
                $this->isImage = false;
            }
        }
    }

    /**
     * File upload process
     *
     * @return void
     */
    public function upload()
    {
        // Create the directory if it doesn't exist
        $path = $this->makeDirectory();
        if (!$path) throw new \Exception('File could not be created.');

        // Remove the old file if it exists
        if ($this->old) {
            $this->removeFile();
        }

        // Get the filename
        if (!$this->filename) {
            $this->filename = $this->getFileName();
        }

        // Upload file or image
        if ($this->isImage == true) {
            $this->uploadImage();
        } else {
            $this->uploadFile();
        }
    }

    /**
     * Upload the file if it is an image
     *
     * @return void
     */
    protected function uploadImage()
    {
        try {
            // Load the image using the read() method
            $image = Image::read($this->file);

            // Resize the image if size is specified
            if ($this->size) {
                $size = explode('x', strtolower($this->size));
                $image->scale(width: (int)$size[0], height: (int)$size[1]);
            }

            // Save the image
            $image->save($this->path . '/' . $this->filename);

            // Save the image as a thumbnail version
            if ($this->thumb) {
                if ($this->old) {
                    $this->removeFile($this->path . '/thumb_' . $this->old);
                }
                $thumb = explode('x', $this->thumb);
                Image::read($this->file)
                    ->scale(width: (int)$thumb[0], height: (int)$thumb[1])
                    ->save($this->path . '/thumb_' . $this->filename);
            }
        } catch (\Exception $e) {
            throw new \Exception('Image upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Upload the file if it is not an image
     *
     * @return void
     */
    protected function uploadFile()
    {
        $this->file->move($this->path, $this->filename);
    }

    /**
     * Make directory if it doesn't exist
     * Developer can also call this method statically
     *
     * @param $location
     * @return string
     */
    public function makeDirectory($location = null)
    {
        if (!$location) $location = $this->path;
        if (file_exists($location)) return true;
        return mkdir($location, 0755, true);
    }

    /**
     * Remove all directories inside the location
     * Developer can also call this method statically
     *
     * @param $location
     * @return void
     */
    public function removeDirectory($location = null)
    {
        if (!$location) $location = $this->path;
        if (!is_dir($location)) {
            throw new \InvalidArgumentException("$location must be a directory");
        }
        if (substr($location, strlen($location) - 1, 1) != '/') {
            $location .= '/';
        }
        $files = glob($location . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                static::removeDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($location);
    }

    /**
     * Remove the file if it exists
     * Developer can also call this method statically
     *
     * @param $path
     * @return void
     */
    public function removeFile($path = null)
    {
        if (!$path) $path = $this->path . '/' . $this->old;

        if (file_exists($path) && is_file($path)) {
            unlink($path);
        }

        if ($this->thumb) {
            $thumbPath = $this->path . '/thumb_' . $this->old;
            if (file_exists($thumbPath) && is_file($thumbPath)) {
                unlink($thumbPath);
            }
        }
    }

    /**
     * Generating the filename which is being uploaded
     *
     * @return string
     */
    protected function getFileName()
    {
        return uniqid() . time() . '.' . $this->file->getClientOriginalExtension();
    }

    /**
     * Get access of array from fileInfo method as a non-static method.
     * Also get some other methods
     *
     * @return string|void
     */
    public function __call($method, $args)
    {
        $fileInfo = new FileInfo;
        $filePaths = $fileInfo->fileInfo();
        if (array_key_exists($method, $filePaths)) {
            $path = json_decode(json_encode($filePaths[$method]));
            return $path;
        } else {
            if (method_exists($this, $method)) {
                $this->$method(...$args);
            } else {
                throw new \Exception('File key or method doesn\'t exist.');
            }
        }
    }

    /**
     * Get access to some non-static methods as static methods
     *
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        $selfClass = new FileManager;
        $selfClass->$method(...$args);
    }
}