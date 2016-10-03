<?php
namespace Arrow\Contracts\Http\Response;

use Arrow\Contracts\Structs\ParameterBagInterface;

interface ResponseCookiesInterface extends ParameterBagInterface
{
	/*
	*@return ResponseCookieInterface
	*/
	public function get($key, $default = null);
}