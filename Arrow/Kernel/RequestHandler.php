<?php
namespace Arrow\Kernel;

use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
use Arrow\Kernel\Events\TerminateEvent;
use Arrow\Kernel\Events\GetResponseEvent;
use Arrow\Kernel\Events\FilterResponseEvent;
use Arrow\Kernel\Events\GetControllerEvent;
use Arrow\Kernel\Events\GetResponseForControllerEvent;
use Arrow\Kernel\Events\GetResponseForExceptionEvent;
use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;
use Arrow\Contracts\Kernel\RequestHandlerInterface;
use Arrow\Kernel\Exception\NotFoundHttpException;

class RequestHandler implements RequestHandlerInterface
{
	protected $dispatcher;
	public function __construct(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	public function handle(RequestInfoInterface $request, $catch = true)
	{
		try
		{
			return $this->doHandle($request);
		}
		catch (\Exception $e)
		{
			if (false == $catch) throw $e;
			return $this->handleException($e, $request);
		}
	}
	public function terminate(RequestInfoInterface $request, SenderWithDestinationInterface $response)
	{
		$this->dispatcher->dispatch(new TerminateEvent($request, $response));
	}
	public function terminateWithException(\Exception $e, RequestInfoInterface $request)
	{
		$response = $this->handleException($e, $request);
		$response->send();
		$this->terminate($request, $response);
	}
	protected function doHandle(RequestInfoInterface $request)
	{
		$event = new GetResponseEvent($request);
		$this->dispatcher->dispatch($event);
		if ($event->hasResponse())
		{
			return $this->filterResponse($event->getResponse(), $request);
		}
        $event = new GetControllerEvent($request);
        $this->dispatcher->dispatch($event);
		if (!$event->hasController())
		{
			throw new NotFoundHttpException(sprintf('Unable to find the controller for path "%s". Maybe you forgot to add the matching route in your routing configuration?', $request->getUrl()->getPath()), 404);
		}
		$controller = $event->getController();
		$response = call_user_func_array($controller, $event->getArguments());
		if ($response instanceof SenderWithDestinationInterface)
		{
			return $this->filterResponse($response, $request);
		}
		elseif (!$response instanceof SenderWithDestinationInterface)
		{
			$event = new GetResponseForControllerEvent($request, $response);
			$this->dispatcher->dispatch($event);
			if (!$event->hasResponse())
			{
				throw new \LogicException('The listeners for "GetResponseForControllerEvent" must return a instance of "SenderWithDestinationInterface"', 500);
			}
			return $this->filterResponse($event->getResponse(), $request);
		}
		else
		{
			throw new \LogicException('Response object not generated', 500);
		}
	}
	protected function filterResponse(SenderWithDestinationInterface $response, RequestInfoInterface $request)
	{
		$event = new FilterResponseEvent($request, $response);
		$this->dispatcher->dispatch($event);
		return $event->getResponse();
	}
	protected function handleException(\Exception $e, $request)
	{
		$event = new GetResponseForExceptionEvent($e, $request);
		$this->dispatcher->dispatch($event);
		//impossible manage exception
		if (!$event->hasResponse()) throw $e;
		$response = $event->getResponse();
		try
		{
			//return a possible new response
			return $this->filterResponse($response, $request);
		}
		catch (\Exception $e)
		{
			//return the original response
			return $response;
		}
	}
}