<?php
namespace Arrow\Contracts\Http\Response;

interface ResponseHeadersInterface
{
	public function sendStatus();
	public function getStatus();
	public function getProtocol();
	public function getStatusText();
	public function setStatus($status);
	public function setProtocol($protocol);
	public function setStatusText($text);
	/*
	*@return ResponseHeaderValuesInterface
	*/
	public function get($key, $default = null);
	/*
	*@return ResponseHeaderValuesInterface
	*/
	public function set($key, $value);
}