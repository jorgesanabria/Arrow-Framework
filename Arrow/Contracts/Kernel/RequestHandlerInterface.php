<?php
namespace Arrow\Contracts\Kernel;

use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;

interface RequestHandlerInterface
{
	public function handle(RequestInfoInterface $request, $catch = true);
	public function terminate(RequestInfoInterface $request, SenderWithDestinationInterface $response);
	public function terminateWithException(\Exception $e, RequestInfoInterface $request);
}