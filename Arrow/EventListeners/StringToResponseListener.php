<?php
namespace Arrow\EventListeners;

use Arrow\Contracts\EventDispatcher\SubscriberInterface;
use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
use Arrow\Kernel\Events\GetResponseForControllerEvent;
use Arrow\Kernel\Events\KernelEvents;
use Arrow\Http\Response\Response;

class StringToResponseListener implements SubscriberInterface
{
	public function onKernelStringResponse(GetResponseForControllerEvent $ev)
	{
		if (is_string($ev->getContent()))
		{
			$res = new Response();
			$res['body']->write($ev->getContent());
			$ev->setResponse($res);
			$ev->stopPropagation();
		}
	}
	public function subscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->addListener(KernelEvents::VIEW, [$this, 'onKernelStringResponse'], 100);
	}
	public function unsubscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->removeListener(KernelEvents::VIEW, [$this, 'onKernelStringResponse']);
	}
}