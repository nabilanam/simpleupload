<?php

namespace NabilAnam\SimpleUpload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class SimpleUpload
{
	private $file = null;
	private $fileBase64 = null;
	private $dirName = null;
	private $fileName = null;
	private $diskName = null;
	private $previousPath = null;
	private $width = null;
	private $height = null;
	private $keepAspectRatio = false;
	private $skipYear = false;
	private $skipMonth = false;
	private $skipDay = false;
	private $callback = null;

	/**
	 * Laravel form file
	 *
	 * @param UploadedFile $file
	 * @return SimpleUpload
	 */
	public function file($file)
	{
		$this->file = $file;

		return $this;
	}

	/**
	 * Base64 file string
	 *
	 * @param string $base64String
	 * @return SimpleUpload
	 */
	public function fileBase64($base64String)
	{
		$this->fileBase64 = $base64String;

		return $this;
	}

	/**
	 * Final name for this uploaded file.
	 * Default is unique file name.
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
	 * The parent directory for file
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
	 * Tries to maintain aspect ration. Set width, height with resizeImage function.
	 *
	 * @param bool $flag
	 * @return SimpleUpload
	 */
	public function keepAspectRatio($flag = true)
	{
		$this->keepAspectRatio = $flag;

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
	 * For final path skip year, month, day directory.
	 *
	 * @param boolean $year
	 * @param boolean $month
	 * @param boolean $day
	 * @return SimpleUpload
	 */
	public function skipDirectory($year = true, $month = true, $day = true)
	{
		$this->skipYear = $year;
		$this->skipMonth = $month;
		$this->skipDay = $day;

		return $this;
	}

	/**
	 * For final path skip year directory.
	 *
	 * @return SimpleUpload
	 */
	public function skipYear()
	{
		$this->skipYear = true;

		return $this;
	}

	/**
	 * For final path skip month directory.
	 *
	 * @return SimpleUpload
	 */
	public function skipMonth()
	{
		$this->skipMonth = true;

		return $this;
	}

	/**
	 * For final path skip day directory.
	 *
	 * @return SimpleUpload
	 */
	public function skipDay()
	{
		$this->skipDay = true;

		return $this;
	}

	/**
	 * Work with underlying intervention image object.
	 * Callback receives intervention image object and it must return the modified image object.
	 *
	 * @param function callback
	 * @return SimpleUpload
	 */
	public function intervention($callback)
	{
		$this->callback = $callback;

		return $this;
	}

	/**
	 * Saves file and returns path.
	 * Terminal operation for file upload.
	 *
	 * @return string filepath
	 */
	public function save()
	{
		if ($this->file || $this->fileBase64) {
			if (!$this->diskName) {
				$this->diskName = config('simpleupload.default_disk');
			}

			$path = null;
			$uploadDir = $this->getUploadDir();

			if ($this->previousPath && Storage::disk($this->diskName)->exists($this->previousPath) && !Str::startsWith($this->previousPath, config('simpleupload.protected_directory'))) {
				Storage::disk($this->diskName)->delete($this->previousPath);
			}

			if ($this->fileBase64) {
				$path = $this->decodeBase64($uploadDir);
				Storage::disk($this->diskName)->put($path, $this->fileBase64);
			} else if ($this->file && $this->fileName) {
				$path = $this->file->storeAs($uploadDir, $this->fileName . '.' . $this->file->clientExtension(), $this->diskName);
			} else if ($this->file) {
				$path = $this->file->store($uploadDir, $this->diskName);
				if ($path == null) {
					$path = $this->previousPath;
				}
			}

			$img = null;
			if ($this->width || $this->height) {
				$img = Image::make($path);
				if ($this->keepAspectRatio) {
					$img = $img->fit($this->width == null ? $this->height : $this->width, $this->height);
				} else {
					$img = $img->resize($this->width, $this->height);
				}
			}

			if ($this->callback) {
				if ($img == null) {
					$img = Image::make($path);
				}

				$cb = $this->callback;
				$img = $cb($img);
			}

			if ($img) {
				$img->save($path);
			}

			return $path;
		}

		return $this->previousPath;
	}

	private function getUploadDir()
	{
		$uploadDir = config('simpleupload.root_directory') . '/' . $this->dirName . '/';

		if (!$this->skipYear) {
			$uploadDir .= '/' . date('Y') . '/';
		}
		if (!$this->skipMonth) {
			$uploadDir .= '/' . date('m') . '/';
		}
		if (!$this->skipDay) {
			$uploadDir .= '/' . date('d') . '/';
		}

		return preg_replace('!//+!', '/', $uploadDir);
	}

	private function decodeBase64($uploadDir)
	{
		if ($this->fileName) {
			$path = $uploadDir . $this->fileName;
		} else {
			$path = $uploadDir . uniqid() . '.' . explode('/', explode(':', substr($this->fileBase64, 0, strpos($this->fileBase64, ';')))[1])[1];
		}

		$this->fileBase64 = base64_decode(str_replace(' ', '+', str_replace(substr($this->fileBase64, 0, strpos($this->fileBase64, ',') + 1), '', $this->fileBase64)));

		return $path;
	}
}
