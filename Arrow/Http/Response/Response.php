<?php
namespace Arrow\Http\Response;

use Arrow\Contracts\Http\Response\ResponseBodyInterface;
use Arrow\Contracts\Http\Response\ResponseHeadersInterface;
use Arrow\Contracts\Http\Response\ResponseCookiesInterface;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;

class Response implements SenderWithDestinationInterface, \ArrayAccess
{
	const SEND_HEADERS = true;
	const NOT_SEND_HEADERS = false;
	protected $values = [];
	public function __construct(
		ResponseBodyInterface $body = null,
		ResponseHeadersInterface $headers = null,
		ResponseCookiesInterface $cookies = null
	)
	{
		$this->values['body'] = ($body == null)? (new ResponseBody(fopen('php://temp', 'rwb'))):$body;
		$this->values['headers'] = ($headers == null)? (new ResponseHeaders()):$headers;
		$this->values['cookies'] = ($cookies == null)? (new ResponseCookies()):$cookies;
	}
	public function offsetSet($key, $value)
	{
		if((isset($this->values[$key]) &&
			is_object($this->values[$key]) &&
			!(new \ReflectionClass(get_class($this->values[$key])))->isInstance($value))
		  )
		{
			throw new \LogicException(sprintf('the "%s" member must be a instance of "%s"', $key, get_class($this->values[$key])), 500);
		}
		$this->values[$key] = $value;
	}
	public function offsetGet($key)
	{
		return $this->values[$key];
	}
	public function offsetExists($key)
	{
		return isset($this->values[$key]);
	}
	public function offsetUnset($key)
	{
		if (in_array($key, ['body', 'headers', 'cookies']))
		{
			throw new \LogicException(sprintf('The key "%s" is read only', $key), 500);
		}
	}
	public function send(ResponseBodyInterface $destination, $sendHeaders = self::SEND_HEADERS)
	{
		if (self::SEND_HEADERS == (bool) $sendHeaders)
		{
			$this['headers']->sendStatus();
			$this['headers']->send();
			$this['cookies']->send();
		}
		$this['body']->send($destination);
	}
	public function __destruct()
	{
		fclose($this['body']->getResource());
	}
}