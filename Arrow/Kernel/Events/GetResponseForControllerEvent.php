<?php
namespace Arrow\Kernel\Events;

use Arrow\Kernel\Events\GetResponseEvent;
use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;

class GetResponseForControllerEvent extends GetResponseEvent implements KernelEvents
{
	protected $content;
	public function __construct(RequestInfoInterface $request, $content)
	{
		parent::__construct($request);
		$this->content = $content;
	}
	public function getContent()
	{
		return $this->content;
	}
	public function setContent($content)
	{
		$this->content = $content;
	}
	public function getEventName()
	{
		return static::VIEW;
	}
}