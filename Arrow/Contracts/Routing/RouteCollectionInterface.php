<?php
namespace Arrow\Contracts\Routing;

interface RouteCollectionInterface
{
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function match($path, $action);

	/**
	*	@param string $prefix
	*	@param RouteCollectionInterface $collection
	*	@return void
	*/
	public function join($prefix = '', RouteCollectionInterface $collection);
}