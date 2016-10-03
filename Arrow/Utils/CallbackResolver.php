<?php
namespace Arrow\Utils;

use Arrow\Contracts\Utils\CallbackResolverInterface;

class CallbackResolver implements CallbackResolverInterface
{
	public function resolve($action)
	{
		if ($action instanceof \Closure)
		{
			return $action;
		}
		elseif (is_callable($action))
		{
			if (is_string($action) && false !== strpos($action, '::'))
			{
				return explode('::', $action);
			}
			else //is function
			{
				return $action;
			}
		}//offset 0 for "@" is invalid
		elseif (is_string($action) && false != strpos($action, '@'))
		{
			$action = explode('@', $action);
			if (class_exists($action[0], true))
			{
				return [new $action[0], $action[1]];
			}
		}
		throw new \LogicException('The element is not a valid callable', 500);
	}
}