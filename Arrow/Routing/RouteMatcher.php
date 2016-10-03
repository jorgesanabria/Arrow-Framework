<?php
namespace Arrow\Routing;

use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Routing\RouteCompilerInterface;
use Arrow\Contracts\Routing\RouteInterface;
use Arrow\Contracts\Routing\RouteMatcherInterface;

class RouteMatcher implements RouteMatcherInterface
{
	protected $compiler;
	public function __construct(RouteCompilerInterface $compiler)
	{
		$this->compiler = $compiler;
	}
	public function match(RequestInfoInterface $request, RouteInterface $route, array &$reference = null)
	{
		$regexp = $this->compiler->compile($route);
		if ($route->getSchema() == $request->getUrl()->getSchema() &&
			in_array($request->getMethod(), $route->getMethods()) &&
			$route->getRequestType() == $request->getType() &&
			true == $this->assertConditions($route->getConditions(), $request) &&
			preg_match($regexp, $request->getUrl()->getPath(), $capture)
			)
		{
			if ($reference !== null)
			{
				//merge defaults
				$reference = $route->getValues();
				foreach ($capture as $key=>$value)
				{
					if (!is_string($key)) continue;//We ignore the non-associative keys
					$reference[$key] = $value;
				}
			}
			return true;
		}
		return false;
	}
	protected function assertConditions(array $conditions, RequestInfoInterface $request)
	{
		foreach ($conditions as $condition)
		{
			if (false == call_user_func($condition, $request)) return false;
		}
		return true;
	}
}