<?php
namespace Arrow\Structs;

use Arrow\Contracts\Structs\ParameterBagInterface;

class ParameterBag implements ParameterBagInterface, \IteratorAggregate
{
	protected $values = [];
	public function __construct(array $values = [])
	{
		$this->values = $values;
	}
	public function get($key, $default = null)
	{
		return isset($this->values[$key])? $this->values[$key]:$default;
	}
	public function set($key, $value)
	{
		$this->values[$key] = $value;
	}
	public function remove($key)
	{
		unset($this->values[$key]);
	}
	public function has($key)
	{
		return isset($this->values[$key]);
	}
	public function all()
	{
		return $this->values;
	}
	public function getIterator()
	{
		return new \ArrayIterator($this->values);
	}
}