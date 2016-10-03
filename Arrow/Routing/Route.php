<?php
namespace Arrow\Routing;

use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Routing\RouteInterface;

class Route implements RouteInterface
{
	protected $path;
	protected $action;
	protected $prefix;
	protected $methods;
	protected $requestType;
	protected $schema;
	protected $values;
	protected $rules;
	protected $converters;
	protected $beforeMiddleware;
	protected $afterMiddleware;
	protected $conditions;
	public function __construct($path = null, $action = null, RouteInterface $defaults = null)
	{
		$this->path = $path;
		$this->action = $action;
		if ($defaults != null)
		{
			$this->prefix = $defaults->getPrefix();
			$this->methods = $defaults->getMethods();
			$this->requestType = $defaults->getRequestType();
			$this->schema = $defaults->getSchema();
			$this->values = $defaults->getValues();
			$this->rules = $this->getRules();
			$this->converters = $defaults->getConverters();
			$this->beforeMiddleware = $defaults->getBeforeMiddleware();
			$this->afterMiddleware = $defaults->getAfterMiddleware();
			$this->conditions = $defaults->getConditions();
		}
		else
		{
			$this->prefix = '';
			$this->methods = [];
			$this->requestType = RequestInfoInterface::MASTER_REQUEST;
			$this->schema = 'http';
			$this->values = $this->rules = $this->converters = $this->beforeMiddleware = $this->afterMiddleware = $this->conditions = [];
		}
	}
	public function getPath()
	{
		return $this->path;
	}
	public function getAction()
	{
		return $this->action;
	}
	public function getPrefix()
	{
		return $this->prefix;
	}
	public function getMethods()
	{
		return $this->methods;
	}
	public function setMethods(array $methods)
	{
		$this->methods = array_unique(array_merge($this->methods, array_map('strtoupper', $methods)));
	}
	public function getRequestType()
	{
		return $this->requestType;
	}
	public function getSchema()
	{
		return $this->schema;
	}
	public function getValues()
	{
		return $this->values;
	}
	public function getRules()
	{
		return $this->rules;
	}
	public function getConverters()
	{
		return $this->converters;
	}
	public function getBeforeMiddleware()
	{
		return $this->beforeMiddleware;
	}
	public function getAfterMiddleware()
	{
		return $this->afterMiddleware;
	}
	public function getConditions()
	{
		return $this->conditions;
	}
	public function setPrefix($prefix)
	{
		$this->prefix = (string) $this->prefix . (string) $prefix;
	}
	public function prefix($prefix)
	{
		$this->setPrefix($prefix);
		return $this;
	}
	public function method()
	{
		if (0 != func_num_args())
		{
			$this->setMethods(func_get_args());
		}
		return $this;
	}
	public function master()
	{
		$this->requestType = RequestInfoInterface::MASTER_REQUEST;
		return $this;
	}
	public function sub()
	{
		$this->requestType = RequestInfoInterface::SUB_REQUEST;
	}
	public function http()
	{
		$this->schema = 'http';
	}
	public function https()
	{
		$this->schema = 'https';
	}
	public function value($key, $value)
	{
		$this->values[$key] = $value;
		return $this;
	}
	public function rule($key, $rule)
	{
		$this->rules[$key] = $rule;
		return $this;
	}
	public function convert($key, $callable)
	{
		$this->converters[$key] = $callable;
		return $this;
	}
	public function before($callable, $priority = 0)
	{
		$this->beforeMiddleware[$priority][] = $callable;
		return $this;
	}
	public function after($callable, $priority = 0)
	{
		$this->afterMiddleware[$priority][] = $callable;
		return $this;
	}
	public function condition(callable $condition)
	{
		$this->conditions[] = $condition;
		return $this;
	}
}