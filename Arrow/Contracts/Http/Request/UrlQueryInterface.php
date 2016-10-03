<?php
namespace Arrow\Contracts\Http\Request;
interface UrlQueryInterface
{
	public function get($key, $default = null);
	public function has($key);
}