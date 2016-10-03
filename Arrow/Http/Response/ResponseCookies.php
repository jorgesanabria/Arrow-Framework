<?php
namespace Arrow\Http\Response;

use Arrow\Contracts\Http\Response\SenderInterface;
use Arrow\Contracts\Structs\ParameterBagInterface;

class ResponseCookies implements SenderInterface, ParameterBagInterface, \IteratorAggregate
{
	protected $cookies = [];
	public function send()
	{
		foreach ($this->cookies as $cookie)
		{
			setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
		}
	}
	public function set($key, $value)
	{
		if (is_string($value))
		{
			$this->cookies[$key] = new ResponseCookie(['name'=>$key, 'value'=>$value]);
		}
		elseif (is_array($value))
		{
			$value['name'] = $key;
			$this->cookies[$key] = new ResponseCookie(['name'=>$key, 'value'=>$value]);
		}
	}
	public function get($key, $deault = null)
	{
		return isset($this->cookies[$key])? $this->cookies[$key]:$default;
	}
	public function has($key)
	{
		return isset($this->cookies[$key]);
	}
	public function remove($key)
	{
		unset($this->cookies[$key]);
	}
	public function all()
	{
		return $this->cookies;
	}
	public function getIterator()
	{
		return new \ArrayIterator($this->cookies);
	}
}