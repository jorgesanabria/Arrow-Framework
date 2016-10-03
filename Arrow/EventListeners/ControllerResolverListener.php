<?php
namespace Arrow\EventListeners;

use Arrow\Contracts\EventDispatcher\SubscriberInterface;
use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
use Arrow\Kernel\Events\GetControllerEvent;
use Arrow\Kernel\Events\KernelEvents;
use Arrow\Http\Request\Request;
use Arrow\Contracts\DI\InjectorInterface;
use Arrow\Utils\getReflectorTrait;
use Arrow\Contracts\Utils\CallbackResolverInterface;

class ControllerResolverListener implements SubscriberInterface
{
	use getReflectorTrait;
	protected $resolver;
	protected $injector;
	protected $defaultResponse;
	public function __construct(CallbackResolverInterface $resolver, InjectorInterface $injector)
	{
		$this->resolver = $resolver;
		$this->injector = $injector;
	}
	public function subscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->addListener(KernelEvents::CONTROLLER, [$this, 'onKernelController'], 500);
	}
	public function unsubscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->removeListener(KernelEvents::CONTROLLER, [$this, 'onKernelController']);
	}
	public function onKernelController(GetControllerEvent $ev)
	{
		if ($ev->getRequest() instanceof Request && $ev->getRequest()->offsetExists('__route'))
		{
			$controller = $this->resolver->resolve($ev->getRequest()->offsetGet('__route')->getAction());
			$args = $this->getArguments($controller, $ev->getRequest());
			$ev->setArguments($args);
			$ev->setController($controller);
		}
	}
	protected function getArguments(callable $callable, Request $request)
	{
		$values = $request->offsetGet('arguments')->all();
		$r = $this->getReflector($callable);
		$args = [];
		foreach ($r->getParameters() as $param)
		{
			if (isset($values[$param->name]))
			{
				$args[] = $values[$param->name];
			}
			elseif ($param->getClass() && $this->injector->exists($param->getClass()->getName()))
			{
				$args[] = $this->injector->make($param->getClass()->getName(), $param->name);
			}
			elseif ($param->getClass() && $param->getClass()->isInstance($this->injector))
			{
				$args[] = $this->injector;
			}
			elseif ($param->getClass() && $param->getClass()->isInstance($request))
			{
				$args[] = $request;
			}
			elseif ($param->isDefaultValueAvailable())
			{
				$args[] = $param->getDefaultValue();
			}
		}
		return $args;
	}
}