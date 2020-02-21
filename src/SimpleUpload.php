<?php

namespace nabilanam\SimpleUpload;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class SimpleUpload
{
    private $file = null;
    private $dirName = null;
    private $fileName = null;
    private $diskName = null;
    private $previousPath = null;
    private $width = null;
    private $height = null;

    /**
     * @param \Illuminate\Http\UploadedFile $file
     *
     * @return SimpleUpload
     */
    public function file($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Final name for this uploaded file.
     * Default is client file name.
     *
     * @param string $fileName
     * @return SimpleUpload
     */
    public function fileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Dont add trailing '/' character.
     *
     * @param string $dirName
     * @return SimpleUpload
     */
    public function dirName($dirName)
    {
        $this->dirName = $dirName;

        return $this;
    }

    /**
     * Default is simpleupload disk. Publish config file to change default disk.
     * Dont add trailing '/' character.
     *
     * @param string $diskName
     * @return SimpleUpload
     */
    public function diskName($diskName)
    {
        $this->diskName = $diskName;

        return $this;
    }

    /**
     * Null value is acceptable.
     *
     * @param integer $width
     * @param integer $height
     * @return SimpleUpload
     */
    public function resizeImage($width, $height)
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * Deletes the provided path with current configuration.
     *
     * @param string $previousPath
     * @return SimpleUpload
     */
    public function deleteIfExists($previousPath)
    {
        if ($previousPath) {
            $this->previousPath = $previousPath;
        }

        return $this;
    }

    /**
     * Saves file.
     * Terminary operation for file upload.
     *
     * @return string filepath
     */
    public function save()
    {
        $uploadDir = 'uploads/' . $this->dirName . '/' . date("Y") . '/' . date("m") . '/' . date("d");

        if (!$this->diskName) {
            $this->diskName = config('simpleupload.default_disk');
        }

        if ($this->file) {
            $path = null;

            if ($this->previousPath && Storage::disk($this->diskName)->exists($this->previousPath) && !Str::startsWith($this->previousPath, config('simpleupload.default_directory'))) {
                Storage::disk($this->diskName)->delete($this->previousPath);
            }

            if ($this->fileName) {
                $path = $this->file->storeAs($uploadDir, $this->fileName . '.' . $this->file->clientExtension(), $this->diskName);
            } else {
                $path = $this->file->store($uploadDir, $this->diskName) ?? $this->previousPath;
            }

            if ($this->width || $this->height) {
                $img = Image::make($path);
                $img->resize($this->width, $this->height)->save($path);
            }

            return $path;
        }

        return $this->previousPath;
    }
}
