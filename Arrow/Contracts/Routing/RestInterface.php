<?php
namespace Arrow\Contracts\Routing;

interface RestInterface
{
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function get($path, $action);

	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function head($path, $action);

	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function post($path, $action);
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function put($path, $action);
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function delete($path, $action);
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function trace($path, $action);
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function options($path, $action);
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function connect($path, $action);
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function patch($path, $action);
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function any($path, $action);
}