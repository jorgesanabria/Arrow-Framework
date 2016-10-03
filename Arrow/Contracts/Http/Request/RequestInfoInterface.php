<?php
namespace Arrow\Contracts\Http\Request;

interface RequestInfoInterface
{
	const MASTER_REQUEST = 'MASTER_REQUEST';
	const SUB_REQUEST = 'SUB_REQUEST';
	public function getType();
	public function getMethod();
	public function getUrl();
}