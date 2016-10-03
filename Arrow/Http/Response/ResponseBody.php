<?php
namespace Arrow\Http\Response;

use Arrow\Contracts\Http\Response\ResponseBodyInterface;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;

class ResponseBody implements ResponseBodyInterface, SenderWithDestinationInterface, \IteratorAggregate
{
	protected $resource;
	public function __construct($resource)
	{
		if (!is_resource($resource)) throw new \InvalidArgumentException('The argument passed to construct must be a valid resource');
		$this->resource = $resource;
	}
	public function getResource()
	{
		return $this->resource;
	}
	public function getIterator()
	{
		return new ResponseBodyIterator($this->resource);
	}
	public function send(ResponseBodyInterface $destination)
	{
		rewind($this->resource);
		stream_copy_to_stream($this->resource, $destination->getResource());
	}
	public function write($content)
	{
		fwrite($this->resource, $content);
	}
}