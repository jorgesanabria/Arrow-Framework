<?php
namespace Arrow\Contracts\EventDispatcher;

interface SubscriberInterface
{
	public function subscribe(EventDispatcherInterface $dispatcher);
	public function unsubscribe(EventDispatcherInterface $dispatcher);
}