<?php
namespace Arrow\Routing;

use Arrow\Contracts\Routing\RouteCollectionInterface;
use Arrow\Contracts\Routing\RestInterface;
use Arrow\Contracts\Routing\RouteInterface;

class RouteCollection implements RouteCollectionInterface, RestInterface, \IteratorAggregate
{
	protected $routes;
	protected $default;
	public function __construct(RouteInterface $default)
	{
		$this->routes = [];
		$this->default = $default;
	}
	public function match($path, $action)
	{
		$route = new Route($path, $action, $this->default);
		$this->routes[] = $route;
		return $route;
	}
	public function join($prefix = '', RouteCollectionInterface $collection)
	{
		foreach ($collection as $route)
		{
			$route->setPrefix($prefix);
			$this->routes[] = $route;
		}
	}
	public function getIterator()
	{
		return new \ArrayIterator($this->routes);
	}
	public function get($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['GET']);
		return $route;
	}
	public function head($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['HEAD']);
		return $route;
	}
	public function post($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['POST']);
		return $route;
	}
	public function put($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['PUT']);
		return $route;
	}
	public function delete($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['DELETE']);
		return $route;
	}
	public function trace($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['TRACE']);
		return $route;
	}
	public function options($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['OPTIONS']);
		return $route;
	}
	public function connect($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['CONNECT']);
		return $route;
	}
	public function patch($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['PATCH']);
		return $route;
	}
	public function any($path, $action)
	{
		$route = $this->match($path, $action);
		$route->setMethods(['GET', 'HEAD','POST','PUT','DELETE','TRACE','OPTIONS','CONNECT','PATCH']);
		return $route;
	}
	public function __call($method, array $args)
	{
		if (method_exists($this->default, $method))
		{
			foreach ($this->routes as $route)
			{
				call_user_func_array([$route, $method], $args);
			}
			return call_user_func_array([$this->default, $method], $args);
		}
	}
}