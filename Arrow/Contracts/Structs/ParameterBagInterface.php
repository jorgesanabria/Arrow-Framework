<?php
namespace Arrow\Contracts\Structs;
interface ParameterBagInterface
{
	public function get($key, $default = null);
	public function set($key, $value);
	public function remove($key);
	public function has($key);
	public function all();
}