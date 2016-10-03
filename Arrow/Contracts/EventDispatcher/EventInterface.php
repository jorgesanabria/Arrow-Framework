<?php
namespace Arrow\Contracts\EventDispatcher;
interface EventInterface
{
	public function isPropagationStopped();
	public function stopPropagation();
	public function hasParent();
	public function getParent();
	public function getEventName();
}