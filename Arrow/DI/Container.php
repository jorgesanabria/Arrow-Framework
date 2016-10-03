<?php
namespace Arrow\DI;

use Arrow\Contracts\DI\InjectorInterface;

class Container implements InjectorInterface, \ArrayAccess, \IteratorAggregate
{
	protected $members 		 = [];
	protected $factories 	 = [];
	protected $protected 	 = [];
	protected $dependencys	 = [];
	public function offsetSet($offset, $value)
	{
		if (isset($this->members[$offset]))
		{
			if(is_object($this->members[$offset])) throw new \RunTimeException(sprintf('The offset "%s" is not rewriteable',  $offset));
		}
		$this->members[$offset] = $value;
	}
	public function offsetExists($offset)
	{
       return isset($this->keys[$offset]);
    }
	public function offsetUnset($offset)
	{
    	$key = array_search($this->members[$offset], $this->factories, true);
    	if ($key !== false) unset($this->factories[$key]);
    	$key = array_search($this->members[$offset], $this->protected, true);
    	if ($key !== false) unset($this->protected[$key]);
    	unset($this->keys[$offset], $this->members[$offset]);
	}
	public function offsetGet($offset)
	{
		if (!isset($this->members[$offset])) throw new \InvalidArgumentException(sprintf('The offset "%s" is not defined', $offset));
		if (in_array($this->members[$offset], $this->protected, true) || !$this->members[$offset] instanceof \Closure)
		{
			return $this->members[$offset];
		}
		if (in_array($this->members[$offset], $this->factories, true))
		{
			return $this->members[$offset]($this);
		}
		$this->members[$offset] = $this->members[$offset]($this);
		return $this->members[$offset];
	}
	public function getIterator()
	{
		return new \ArrayIterator($this->members);
	}
	public function factory(\Closure $closure)
	{
		$this->factories[] = $closure;
		return $closure;
	}
	public function protect(\Closure $closure)
	{
		$this->protected[] = $closure;
		return $closure;
	}
	public function raw($key)
	{
		return isset($this->members[$key])? $this->members[$key]:null;
	}
	public function bind($namespace, callable $callback)
	{
		$this->dependencys[$namespace] = $callback;
	}
	public function exists($namespace)
	{
		return isset($this->dependencys[$namespace]);
	}
	public function make($namespace, $name)
	{
		if (!$this->exists($namespace)) throw new \LogicException(sprintf('The class "%s" is not exists', $namespace), 500);
		return call_user_func($this->dependencys[$namespace], $this, $name);
	}
}