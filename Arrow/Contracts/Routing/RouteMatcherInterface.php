<?php
namespace Arrow\Contracts\Routing;

use Arrow\Contracts\Http\Request\RequestInfoInterface;

interface RouteMatcherInterface
{
	/**
	*	@param RequestInfoInterface $request
	*	@param RouteInterface
	*	@return boolean
	*/
	public function match(RequestInfoInterface $request, RouteInterface $route, array &$reference = null);
}