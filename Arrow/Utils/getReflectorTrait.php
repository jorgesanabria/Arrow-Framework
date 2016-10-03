<?php
namespace Arrow\Utils;

use ReflectionFunction;
use ReflectionParameter;
use ReflectionClass;
use ReflectionMethod;

trait getReflectorTrait
{
	protected function getReflector(callable $callable)
	{
		if (is_array($callable))
		{
			$r = new ReflectionMethod($callable[0], $callable[1]);
		}
		elseif (is_string($callable) && strpos($callable, '::'))
		{
			$callable = explode('::', $callable);
			$r = new ReflectionMethod($callable[0], $callable[1]);
		}
		elseif (is_string($callable) || $callable instanceof \Closure)
		{
			$r = new ReflectionFunction($callable);
		}
		return $r;
	}
}