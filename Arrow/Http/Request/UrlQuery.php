<?php
namespace Arrow\Http\Request;

use Arrow\Contracts\Http\Request\UrlQueryInterface;

class UrlQuery implements UrlQueryInterface
{
	protected $values;
	public function __construct($query)
	{
		parse_str($query, $output);
		$this->values = $output;
	}
	public function get($key, $default = null)
	{
		return isset($this->values[$key])? $this->values[$key]:$default;
	}
	public function has($key)
	{
		return isset($this->values[$key]);
	}
}