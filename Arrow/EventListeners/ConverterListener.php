<?php
namespace Arrow\EventListeners;

use Arrow\Contracts\EventDispatcher\SubscriberInterface;
use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
use Arrow\Kernel\Events\GetResponseEvent;
use Arrow\Kernel\Events\KernelEvents;
use Arrow\Kernel\Events\GetControllerEvent;
use Arrow\Http\Request\Request;
use Arrow\Contracts\Utils\CallbackResolverInterface;

class ConverterListener implements SubscriberInterface
{
	protected $resolver;
	public function __construct(CallbackResolverInterface $resolver)
	{
		$this->resolver = $resolver;
	}
	public function subscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->addListener(KernelEvents::CONTROLLER, [$this, 'onKernelController'], 600);
	}
	public function unsubscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->removeListener(KernelEvents::CONTROLLER, [$this, 'onKernelController']);
	}
	public function onKernelController(GetControllerEvent $ev)
	{
		if ($ev->getRequest() instanceof Request && $ev->getRequest()->offsetExists('__route'))
		{
			$converters = $ev->getRequest()->offsetGet('__route')->getConverters();
			$values = $ev->getArguments();
			foreach ($converters as $key=>$action)
			{
				$action = $this->resolver->resolve($action);
				if (!isset($values[$key])) continue;
				$values[$key] = call_user_func($action, $values[$key]);
			}
			$ev->setArguments($values);
		}
	}
}