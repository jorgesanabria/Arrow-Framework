<?php
namespace Arrow\Contracts\ServiceProvider;

use Arrow\Application;

interface ServiceProviderInterface
{
	public function register(Application $app);
	public function boot(Application $app);
}