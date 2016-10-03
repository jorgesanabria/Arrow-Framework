<?php

class ResponseBodyIterator implements \Iterator
{
	protected $resource;
	protected $id;
	public function __construct($resource)
	{
		if (!is_resource($resource))
		{
			throw new \InvalidArgumentException('the argument passed to the constructor must be a valid resource', 500);
		}
		$this->resource = $resource;
		$this->id = 0;
	}
	public function current()
	{
		return fgets($this->resource);
	}
	public function key()
	{
		return $this->key;
	}
	public function next()
	{
		$this->id++;
	}
	public function rewind()
	{
		rewind($this->resource);
		$this->id = 0;
	}
	public function valid()
	{
		return !feof($this->resource);
	}
}