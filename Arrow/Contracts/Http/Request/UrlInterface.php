<?php
namespace Arrow\Contracts\Http\Request;

interface UrlInterface
{
	public function getSchema();
	public function getUser();
	public function getPassword();
	public function getHost();
	public function getPort();
	public function getPath();
	public function getQuery();
	public function getFragment();
}