<?php
namespace Arrow\Contracts\DI;

interface InjectorInterface
{
	public function bind($namespace, callable $callback);
	public function exists($namespace);
	public function make($namespace, $name);
}