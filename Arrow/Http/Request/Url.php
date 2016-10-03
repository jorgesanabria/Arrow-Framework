<?php
namespace Arrow\Http\Request;

use Arrow\Contracts\Http\Request\UrlInterface;

class Url implements UrlInterface
{
	protected $schema;
	protected $user;
	protected $password;
	protected $host;
	protected $port;
	protected $path;
	protected $query;
	protected $fragment;
	public function __construct($url)
	{
		$parts = array_merge([
			'schema'=>'http',
			'user'=>'',
			'password'=>'',
			'host'=>'localhost',
			'port'=>80,
			'path'=>'/',
			'query'=>'',
			'fragment'=>''
		], parse_url($url));
		foreach ($parts as $key=>$value)
		{
			$this->$key = $value;
		}
		$this->query = new UrlQuery($this->query);
	}
	public function getSchema()
	{
		return $this->schema;
	}
	public function getUser()
	{
		return $this->user;
	}
	public function getPassword()
	{
		return $this->password;
	}
	public function getHost()
	{
		return $this->host;
	}
	public function getPort()
	{
		return $this->port;
	}
	public function getPath()
	{
		return $this->path;
	}
	public function getQuery()
	{
		return $this->query;
	}
	public function getFragment()
	{
		return $this->fragment;
	}
}