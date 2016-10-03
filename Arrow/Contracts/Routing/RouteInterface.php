<?php
namespace Arrow\Contracts\Routing;

interface RouteInterface
{
	/**
	*	@return string
	*/
	public function getPath();

	/**
	*	@return string
	*/
	public function getPrefix();

	/**
	*	@param string $prefix
	*	@return void
	*/
	public function setPrefix($prefix);

	/**
	*	@return mixed
	*/
	public function getAction();

	/**
	*	@return array
	*/
	public function getMethods();

	/**
	*	@param string[] $methods
	*	@return void
	*/
	public function setMethods(array $methods);

	/**
	*	@return string
	*/
	public function getRequestType();

	/**
	*	@return string
	*/
	public function getSchema();

	/**
	*	@return array
	*/
	public function getValues();

	/**
	*	@return array
	*/
	public function getRules();

	/**
	*	@return array
	*/
	public function getConverters();

	/**
	*	@return array
	*/
	public function getBeforeMiddleware();

	/**
	*	@return array
	*/
	public function getAfterMiddleware();

	/**
	*	@return array
	*/
	public function getConditions();
}