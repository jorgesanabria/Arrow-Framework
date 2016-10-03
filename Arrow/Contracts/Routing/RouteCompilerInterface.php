<?php
namespace Arrow\Contracts\Routing;

interface RouteCompilerInterface
{
	/**
	*	@param RouteInterface $route
	*	@return string
	*/
	public function compile(RouteInterface $route);
}