<?php
namespace Arrow\Contracts\EventDispatcher;
interface EventDispatcherInterface
{
	public function addSubscriber(SubscriberInterface $suscriber);
	public function removeSubscriber(SubscriberInterface $suscriber);
	public function addListener($eventName, callable $listener, $priority = 0);
	public function removeListener($eventName, callable $listener);
	public function dispatch(EventInterface $event);
}
