<?php
namespace Arrow\Http\Response;

use Arrow\Contracts\Http\Response\ResponseHeadersInterface;
use Arrow\Contracts\Http\Response\SenderInterface;
use Arrow\Contracts\Structs\ParameterBagInterface;

class ResponseHeaders implements ResponseHeadersInterface, ParameterBagInterface, SenderInterface, \IteratorAggregate
{
	const STATUS_OK = 200;
	const STATUS_OK_MESSAGE = 'OK';
	const DEFAULT_PROTOCOL = '1.1';
	protected $statusCode = self::STATUS_OK;
	protected $statusText = self::STATUS_OK_MESSAGE;
	protected $protocol = self::DEFAULT_PROTOCOL;
	protected $headers = [];
	public function send()
	{
		foreach ($this->headers as $name=>$values)
		{
			foreach ($values as $value)
			{
				header(sprintf('%s: %s', $name, $value), false, $this->statusCode);
			}
		}
	}
	public function sendStatus()
	{
		header(sprintf('HTTP/%s %s %s', $this->getProtocol(), $this->getStatus(), $this->getStatus()), true, $this->getStatus());
	}
	public function getStatus()
	{
		return $this->statusCode;
	}
	public function getProtocol()
	{
		return $this->protocol;
	}
	public function getStatusText()
	{
		return $this->statusText;
	}
	public function setStatus($status)
	{
		$this->statusCode = $status;
	}
	public function setProtocol($protocol)
	{
		$this->protocol = $protocol;
	}
	public function setStatusText($text)
	{
		$this->statusText = $text;
	}
	public function set($key, $value)
	{
		return $this->headers[$key] = (array) $value;
	}
	public function get($key, $default = null)
	{
		return isset($this->headers[$key])? $this->headers[$key]:$default;
	}
	public function has($key)
	{
		return isset($this->headers[$key]);
	}
	public function remove($key)
	{
		unset($this->headers[$key]);
	}
	public function add($key, $value)
	{
		$merge = array_merge($this->headers[$key], (array) $value);
		$this->headers[$key] = $merge;
	}
	public function all()
	{
		return $this->headers;
	}
	public function getIterator()
	{
		return new \ArrayIterator($this->headers);
	}
}