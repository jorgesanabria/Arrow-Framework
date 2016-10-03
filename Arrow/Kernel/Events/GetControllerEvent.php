<?php
namespace Arrow\Kernel\Events;

use Arrow\EventDispatcher\abstractEvent;
use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Kernel\Exception\NotFoundHttpException;

class GetControllerEvent extends abstractEvent implements KernelEvents
{
	protected $request;
	protected $controller;
	protected $arguments;
	public function __construct(RequestInfoInterface $request)
	{
		parent::__construct(null);
		$this->request = $request;
	}
	public function hasController()
	{
		return is_callable($this->controller);
	}
	public function getController()
	{
		if (!$this->hasController()) throw new NotFoundHttpException('The controller is not defined', 500);
		return $this->controller;		
	}
	public function setController(callable $controller)
	{
		$this->controller = $controller;
	}
	public function getRequest()
	{
		return $this->request;
	}
	public function setArguments(array $arguments)
	{
		$this->arguments = $arguments;
	}
	public function getArguments()
	{
		return $this->arguments;
	}
	public function getEventName()
	{
		return static::CONTROLLER;
	}
}