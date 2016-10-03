<?php
namespace Arrow\Kernel\Events;

use Arrow\Kernel\Events\GetResponseEvent;
use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;

class FilterResponseEvent extends GetResponseEvent implements KernelEvents
{
	public function __construct(RequestInfoInterface $request, SenderWithDestinationInterface $response)
	{
		parent::__construct($request);
		$this->response = $response;
	}
	public function getEventName()
	{
		return static::RESPONSE;
	}
}