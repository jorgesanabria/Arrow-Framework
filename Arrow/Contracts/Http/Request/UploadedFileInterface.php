<?php
namespace Arrow\Contracts\Http\Request;
interface UploadedFileInterface
{
	const UPLOAD_ERR_OK = 0;
	const UPLOAD_ERR_INI_SIZE = 1;
	const UPLOAD_ERR_FORM_SIZE = 2;
	const UPLOAD_ERR_PARTIAL = 3;
	const UPLOAD_ERR_NO_FILE = 4;
	const UPLOAD_ERR_NO_TMP_DIR = 6;
	const UPLOAD_ERR_CANT_WRITE = 7;
	const UPLOAD_ERR_EXTENSION = 8;
	public function getClientName();
	public function getClientType();
	public function getRealType();
	public function getErrorLevel();
	public function getTempName();
	public function getSize();
	public function moveTo($destination);
	public function isOk();
}