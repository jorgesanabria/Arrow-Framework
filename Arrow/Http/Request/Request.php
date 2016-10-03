<?php
namespace Arrow\Http\Request;

use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Http\Request\RequestMatchInterface;
use Arrow\Contracts\Structs\ParameterBagInterface;
use Arrow\Structs\ParameterBag;

class Request implements RequestInfoInterface, RequestMatchInterface, \ArrayAccess
{
	protected static $methods = [
		'HEAD',
		'GET',
		'POST',
		'PUT',
		'PATCH',
		'DELETE',
		'CONNECT',
		'TRACE',
		'OPTIONS'
	];
	protected $type;
	protected $method;
	protected $url;
	protected $scope = [];
	public function __construct($method, $url, array $scope = [], $type = RequestInfoInterface::MASTER_REQUEST)
	{
		$this->type = $type;
		$method = strtoupper($method);
		if (!in_array($method, self::$methods))
		{
			throw new \InvalidArgumentException(sprintf('The method "%s" is not supported', $method), 500);
		}
		$this->method = $method;
		$this->url = new Url($url);
		foreach ($scope as $key=>$value)
		{
			if (!is_string($key))
			{
				throw new \InvalidArgumentException('The keys for "scope" array must be associative keys', 500);
			}
			$this->scope[$key] = $value;
		}
	}

	//request info
	public function getType()
	{
		return $this->type;
	}
	public function getMethod()
	{
		return $this->method;
	}
	public function getUrl()
	{
		return $this->url;
	}

	//array access
	public function offsetSet($key, $value)
	{
		if((isset($this->scope[$key]) &&
			is_object($this->scope[$key]) &&
			!(new \ReflectionClass(get_class($this->scope[$key])))->isInstance($value))
		  )
		{
			throw new \LogicException(sprintf('the "%s" offset must be a instance of "%s"', $key, get_class($this->scope[$key])), 500);
		}
		$this->scope[$key] = $value;
	}
	public function offsetGet($key)
	{
		return $this->scope[$key];
	}
	public function offsetExists($key)
	{
		return isset($this->scope[$key]);
	}
	public function offsetUnset($key)
	{
		unset($this->scope[$key]);
	}

	//match
	public function isMaster()
	{
		return $this->getType() == static::MASTER_REQUEST;
	}
	public function isSub()
	{
		return $this->getType() == static::SUB_REQUEST;
	}
	public function isHttps()
	{
		return $this->getSchema() == 'HTTPS';
	}
	public function isHttp()
	{
		return $this->getSchema() == 'HTTP';
	}
	public function isHead()
	{
		return $this->getMethod() == 'HEAD';
	}
	public function isGet()
	{
		return $this->getMethod() == 'GET';
	}
	public function isPost()
	{
		return $this->getMethod() == 'POST';
	}
	public function isPut()
	{
		return $this->getMethod() == 'PUT';
	}
	public function isDelete()
	{
		return $this->getMethod() == 'DELETE';
	}
	public function isOptions()
	{
		return $this->getMethod() == 'OPTIONS';
	}
	public function isTrace()
	{
		return $this->getMethod() == 'TRACE';
	}
	public function isConnect()
	{
		return $this->getMethod() == 'CONNECT';
	}
	public function isPatch()
	{
		return $this->getMethod() == 'PATCH';
	}
}