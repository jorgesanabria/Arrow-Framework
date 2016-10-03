<?php
namespace Arrow\Contracts\Http\Request;

interface RequestMatchInterface
{
	public function isMaster();
	public function isSub();
	public function isHttps();
	public function isHttp();
	public function isHead();
	public function isGet();
	public function isPost();
	public function isPut();
	public function isDelete();
	public function isOptions();
	public function isTrace();
	public function isConnect();
	public function isPatch();
}