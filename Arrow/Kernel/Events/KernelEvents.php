<?php
namespace Arrow\Kernel\Events;

interface KernelEvents
{
	const REQUEST = 'Kernel.Request';
	const RESPONSE = 'Kernel.Response';
	const VIEW = 'Kernel.View';
	const ERROR = 'Kernel.Error';
	const TERMINATE = 'Kernel.Terminate';
	const CONTROLLER = 'Kernel.Controller';
}