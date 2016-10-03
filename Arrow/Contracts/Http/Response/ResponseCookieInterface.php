<?php
namespace Arrow\Contracts\Http\Response;

interface ResponseCookieInterface
{
	public function getName();
	public function getValue();
	public function getDomain();
	public function getExpiresTime();
	public function getPath();
	public function isSecure();
	public function isHttpOnly();
	public function isCleared();
}