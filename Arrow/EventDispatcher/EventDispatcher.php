<?php
namespace Arrow\EventDispatcher;

use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
use Arrow\Contracts\EventDispatcher\EventInterface;
use Arrow\Contracts\EventDispatcher\SubscriberInterface;

class EventDispatcher implements EventDispatcherInterface
{
	protected $listeners = [];
	public function addSubscriber(SubscriberInterface $suscriber)
	{
		$suscriber->subscribe($this);
	}
	public function removeSubscriber(SubscriberInterface $suscriber)
	{
		$suscriber->unsubscribe($this);
	}
	public function addListener($eventName, callable $listener, $priority = 0)
	{
		$this->listeners[$eventName][$priority][] = $listener;
	}
	public function removeListener($eventName, callable $toRemove)
	{
		
		if (!isset($this->listeners[$eventName])) return;
		$listeners = &$this->listeners[$eventName];
		foreach ($listeners as &$listenerList)
		{
			foreach ($listenerList as $key=>$listener)
			{
				if ($toRemove === $listener)
				{
					unset($listenerList[$key]);
					return;
				}
			}
		}
	}
	public function dispatch(EventInterface $event)
	{
		if (!isset($this->listeners[$event->getEventName()])) return $event;
		foreach ($this->getSortetListeners($event->getEventName()) as $listeners)
		{
			foreach ($listeners as $listener)
			{
				call_user_func($listener, $event, $this);
				if ($event->isPropagationStopped()) return $event;
			}
		}
		return $event;
	}
	protected function getSortetListeners($event)
	{
		$listeners = $this->listeners[$event];
		ksort($listeners);
		return $listeners;
	}
}