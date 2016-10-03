<?php
namespace Arrow\Routing;

use Arrow\Contracts\Routing\RouteCompilerInterface;
use Arrow\Contracts\Routing\RouteInterface;

class RouteCompiler implements RouteCompilerInterface
{
	public function compile(RouteInterface $route)
	{
		$path 	 = $route->getPrefix() . $route->getPath();
		$rules 	 = $route->getRules();
		$pattern = '@(?P<param>\:\w+)@';
		$before  = '';
		$after   = '';
		$cacture = '';
		$rule 	 = '';
		$argName = '';
		$count   = 0;
		while (1 == preg_match($pattern, $path, $cacture))
		{
			$before  = $cacture['param'];
			$argName = substr($before, 1);
			$rule 	 = (!empty($rules[$argName]))? $rules[$argName] : '\w+';
			$after   = sprintf('(?P<%s>%s)', $argName, $rule);
			$path    = str_replace($before, $after, $path, $count);
			if ($count > 1)
			{
				throw new \RuntimeException(sprintf('Error Processing the path "%s"', $path), 500);
			}
		}
		return sprintf('@^%s$@', $path);
	}
}