<?php
namespace Arrow\EventListeners;

use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
use Arrow\Contracts\EventDispatcher\SubscriberInterface;
use Arrow\Contracts\Routing\RouteMatcherInterface;
use Arrow\Contracts\Routing\RouteCollectionInterface;
use Arrow\Kernel\Events\GetResponseEvent;
use Arrow\Kernel\Events\KernelEvents;
use Arrow\Structs\ParameterBag;
use Arrow\Http\Request\Request;

class RouteListener implements SubscriberInterface
{
	protected $matcher;
	public function __construct(RouteMatcherInterface $matcher, RouteCollectionInterface $routes)
	{
		$this->matcher = $matcher;
		$this->routes = $routes;
	}
	public function subscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->addListener(KernelEvents::REQUEST, [$this, 'onKernelRequest'], 500);
	}
	public function unsubscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->removeListener(KernelEvents::REQUEST, [$this, 'onKernelRequest']);
	}
	public function onKernelRequest(GetResponseEvent $ev)
	{
		$request = $ev->getRequest();
		$args = [];
		foreach ($this->routes as $route)
		{
			if ($this->matcher->match($request, $route, $args))
			{
				if ($request instanceof Request)
				{
					$request['arguments'] = new ParameterBag($args);
					$request['__route'] = $route;
				}
				break;
			}
		}
	}
}