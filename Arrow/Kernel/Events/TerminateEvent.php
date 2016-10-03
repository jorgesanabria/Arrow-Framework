<?php
namespace Arrow\Kernel\Events;

use Arrow\EventDispatcher\abstractEvent;
use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;

class TerminateEvent extends abstractEvent implements KernelEvents
{
	protected $request;
	protected $response;
	public function __construct(RequestInfoInterface $request, SenderWithDestinationInterface $response)
	{
		parent::__construct(null);
		$this->request = $request;
		$this->response = $response;
	}
	public function getRequest()
	{
		return $this->request;
	}
	public function getResponse()
	{
		return $this->response;
	}
	public function getEventName()
	{
		return static::TERMINATE;
	}
}