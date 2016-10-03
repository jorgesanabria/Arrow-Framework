<?php
namespace Arrow\EventListeners;

use Arrow\Contracts\EventDispatcher\SubscriberInterface;
use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
use Arrow\Kernel\Events\GetResponseForControllerEvent;
use Arrow\Kernel\Events\KernelEvents;
use Arrow\Http\Response\Response;
use Arrow\Http\Response\ResponseBody;

class ResourceToResponseListener implements SubscriberInterface
{
	public function onKernelResourceResponse(GetResponseForControllerEvent $ev)
	{
		if (is_resource($ev->getContent()))
		{
			$resource = $ev->getContent();
			//rewind($resource);
			$res = new Response(new ResponseBody($resource));
			$ev->setResponse($res);
			$ev->stopPropagation();
		}
	}
	public function subscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->addListener(KernelEvents::VIEW, [$this, 'onKernelResourceResponse'], 100);
	}
	public function unsubscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->removeListener(KernelEvents::VIEW, [$this, 'onKernelResourceResponse']);
	}
}