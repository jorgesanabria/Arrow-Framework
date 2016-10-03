<?php
namespace Arrow\Http\Response;

use Arrow\Contracts\Http\Response\ResponseCookieInterface;

class ResponseCookie
{
	protected $name;
	protected $value;
	protected $domain;
	protected $expire;
	protected $path;
	protected $isSecure;
	protected $httpOnly;
	public function __construct(array $options)
	{
		$defaults = array_merge([
			'name'=>null,
			'value'=>null,
			'domain'=>null,
			'expires'=>null,
			'path'=>null,
			'isSecure'=>null,
			'httpOnly'=>null
		], $options);
		foreach ($defaults as $key=>$value)
		{
			$this->$key = $value;
		}
	}
	public function getName()
	{
		return $this->name;
	}
	public function getValue()
	{
		return $this->value;
	}
	public function getDomain()
	{
		return $this->domain;
	}
	public function getExpiresTime()
	{
		return $this->expire;
	}
	public function getPath()
	{
		return $this->path;
	}
	public function isSecure()
	{
		return $this->isSecure;
	}
	public function isHttpOnly()
	{
		return (bool) $this->httpOnly;
	}
	public function isCleared()
	{
		return $this->expire < time();
	}
}