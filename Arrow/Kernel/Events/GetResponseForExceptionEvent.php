<?php
namespace Arrow\Kernel\Events;

use Arrow\Kernel\Events\GetResponseEvent;
use Arrow\Contracts\Http\Request\RequestInfoInterface;

class GetResponseForExceptionEvent extends GetResponseEvent implements KernelEvents
{
	protected $exception;
	public function __construct(\Exception $e, RequestInfoInterface $request)
	{
		parent::__construct($request);
		$this->exception = $e;
		$this->eventName = 'Error';
	}
	public function getException()
	{
		return $this->exception;
	}
	public function getEventName()
	{
		return static::ERROR;
	}
}