<?php
namespace Arrow\EventDispatcher;

use Arrow\Contracts\EventDispatcher\EventInterface;

abstract class abstractEvent implements EventInterface
{
	private $propagationStatus = true;
	private $parentEvent = null;
	public function __construct(EventInterface $parentEvent = null)
	{
		$this->parentEvent = $parentEvent;
	}
	public function isPropagationStopped()
	{
		return $this->propagationStatus == false;
	}
	public function stopPropagation()
	{
		$this->propagationStatus = false;
	}
	public function hasParent()
	{
		return $this->parentEvent != null;
	}
	public function getParent()
	{
		if (!$this->hasParent()) throw \LogicException('The Parent Event is undefined', 404);
		return $this->parentEvent;
	}
	abstract public function getEventName();
}