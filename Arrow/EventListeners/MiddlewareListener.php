<?php
namespace Arrow\EventListeners;

use Arrow\Contracts\EventDispatcher\SubscriberInterface;
use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
use Arrow\Kernel\Events\GetResponseEvent;
use Arrow\Kernel\Events\FilterResponseEvent;
use Arrow\Http\Request\Request;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;
use Arrow\Kernel\Events\KernelEvents;
use Arrow\Application;
use Arrow\Contracts\Utils\CallbackResolverInterface;

class MiddlewareListener implements SubscriberInterface
{
	protected $app;
	protected $resolver;
	public function __construct(CallbackResolverInterface $resolver, Application $app)
	{
		$this->resolver = $resolver;
		$this->app = $app;
	}
	public function subscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->addListener(KernelEvents::REQUEST, [$this, 'onKernelRequest'], 250);
		$dispatcher->addListener(KernelEvents::RESPONSE, [$this, 'onKernelResponse'], 250);
	}
	public function unsubscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->removeListener(KernelEvents::REQUEST, [$this, 'onKernelRequest']);
		$dispatcher->removeListener(KernelEvents::RESPONSE, [$this, 'onKernelResponse']);
	}
	public function onKernelRequest(GetResponseEvent $ev)
	{
		if ($ev->getRequest() instanceof Request && $ev->getRequest()->offsetExists('__route'))
		{
			$beforeMiddleware = $ev->getRequest()['__route']->getBeforeMiddleware();
			ksort($beforeMiddleware);
			foreach ($beforeMiddleware as $actions)
			{
				foreach ($actions as $action)
				{
					$action = $this->resolver->resolve($action);
					$result = call_user_func($action, $ev->getRequest(), $this->app);
					if ($result instanceof SenderWithDestinationInterface)
					{
						$ev->setResponse($result);
						$ev->stopPropagation();
						break;
					}
				}
			}
		}
	}
	public function onKernelResponse(FilterResponseEvent $ev)
	{
		if ($ev->getRequest() instanceof Request && $ev->getRequest()->offsetExists('__route'))
		{
			$afterMiddleware = $ev->getRequest()['__route']->getAfterMiddleware();
			ksort($afterMiddleware);
			foreach ($afterMiddleware as $actions)
			{
				foreach ($actions as $action)
				{
					$action = $this->resolver->resolve($action);
					$result = call_user_func($action, $ev->getRequest(), $ev->getResponse(), $this->app);
				}
			}
		}
	}
}