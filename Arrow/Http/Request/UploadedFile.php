<?php
namespace Arrow\Http\Request;

use Arrow\Contracts\Http\Request\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
	protected $name;
	protected $size;
	protected $temp_name;
	protected $type;
	public static function getNormalizedFiles(array $files)
	{
		$normalized = [];
        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $normalized[$key] = $value;
                continue;
            }

            if (is_array($value) && isset($value['tmp_name'])) {
                $normalized[$key] = self::createUploadedFileFromSpec($value);
                continue;
            }

            if (is_array($value)) {
                $normalized[$key] = self::filesFromGlobals($value);
                continue;
            }

            throw new \InvalidArgumentException('Invalid value in files specification');
        }
        return $normalized;
	}
	private static function createUploadedFileFromSpec(array $value)
    {
        if (is_array($value['tmp_name'])) {
            return self::normalizeNestedFileSpec($value);
        }

        return new self(
            $value['tmp_name'],
            $value['size'],
            $value['error'],
            $value['name'],
            $value['type']
        );
    }
    private static function normalizeNestedFileSpec(array $files = [])
    {
        $normalizedFiles = [];
        foreach (array_keys($files['tmp_name']) as $key) {
            $spec = [
                'tmp_name' => $files['tmp_name'][$key],
                'size'     => $files['size'][$key],
                'error'    => $files['error'][$key],
                'name'     => $files['name'][$key],
                'type'     => $files['type'][$key],
            ];
            $normalizedFiles[$key] = self::createUploadedFileFromSpec($spec);
        }
        return $normalizedFiles;
    }

    public function __construct($tmp_name, $size, $error, $name, $type)
	{
		$this->name = $name;
		$this->error = $error;
		$this->size = $size;
		$this->temp_name = $tmp_name;
		$this->type = $type;
	}


	//uploadfile
	public function getClientName()
	{
		return $this->name;
	}
	public function getClientType()
	{
		return $this->type;
	}
	public function getRealType()
	{
		$finfo = new \finfo(FILEINFO_MIME_TYPE, '');
		return $finfo->file($this->getTempName());
	}
	public function getErrorLevel()
	{
		return $this->error;
	}
	public function getTempName()
	{
		return $this->temp_name;
	}
	public function getSize()
	{
		return $this->size;
	}
	public function moveTo($destination)
	{
		move_uploaded_file($this->temp_name, $destination);
	}
	public function isOk()
	{
		return static::UPLOAD_ERR_OK == $this->getErrorLevel();
	}
}