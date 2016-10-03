<?php
namespace Arrow\Contracts\Http\Response;

use Arrow\Contracts\Http\Response\ResponseBodyInterface;

interface SenderWithDestinationInterface
{
	public function send(ResponseBodyInterface $destination);
}