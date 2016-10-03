<?php
namespace Arrow\Kernel\Events;

use Arrow\EventDispatcher\abstractEvent;
use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;

class GetResponseEvent extends abstractEvent implements KernelEvents
{
	protected $request;
	protected $response;
	public function __construct(RequestInfoInterface $request)
	{
		parent::__construct(null);
		$this->request = $request;
	}
	public function getRequest()
	{
		return $this->request;
	}
	public function setResponse(SenderWithDestinationInterface $response)
	{
		$this->response = $response;
	}
	public function hasResponse()
	{
		return $this->response instanceof SenderWithDestinationInterface;
	}
	public function getResponse()
	{
		if (!$this->hasResponse()) throw new \LogicException('The Response is undefined', 404);
		return $this->response;
	}
	public function getEventName()
	{
		return static::REQUEST;
	}
}