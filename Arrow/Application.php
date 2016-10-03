<?php
namespace Arrow;

use Arrow\DI\Container;
use Arrow\Contracts\Http\Request\RequestInfoInterface;
use Arrow\Http\Request\Request;
use Arrow\Http\Request\UploadedFile;
use Arrow\Http\Response\Response;
use Arrow\Contracts\Http\Response\SenderWithDestinationInterface;
use Arrow\Http\Response\ResponseBody;
use Arrow\Kernel\RequestHandler;
use Arrow\Kernel\Events\KernelEvents;
use Arrow\EventDispatcher\EventDispatcher;
use Arrow\Contracts\Routing\RestInterface;
use Arrow\Contracts\Routing\RouteCollectionInterface;
use Arrow\Routing\Route;
use Arrow\Routing\RouteCollection;
use Arrow\Routing\RouteCompiler;
use Arrow\Routing\RouteMatcher;
use Arrow\EventListeners\ControllerResolverListener;
use Arrow\EventListeners\RouteListener;
use Arrow\EventListeners\MiddlewareListener;
use Arrow\EventListeners\ConverterListener;
use Arrow\EventListeners\StringToResponseListener;
use Arrow\EventListeners\ResourceToResponseListener;
use Arrow\Utils\CallbackResolver;
use Arrow\Utils\getReflectorTrait;
use Arrow\Exception\AbortApplicationException;
use Arrow\Contracts\ServiceProvider\ServiceProviderInterface;
use Arrow\Contracts\EventDispatcher\EventInterface;

class Application extends Container implements RestInterface, RouteCollectionInterface
{
	use getReflectorTrait;

	private static $booted = false;
	private static $runing = false;

	protected $providers = [];

	/**
	*	@param mixed[] $defaults Optional default values
	*/
	public function __construct(array $defaults = [])
	{
		$app = $this;
		$app['routes.default'] = function()
		{
			return new Route();
		};
		$app['route.collection.factory'] = $app->factory(function($app)
		{
			return new RouteCollection($app['routes.default']);
		});
		$app['routes'] = $app['route.collection.factory'];
		$app['route.compiler'] = function()
		{
			return new RouteCompiler();
		};
		$app['route.matcher'] = function ($app)
		{
			return new RouteMatcher($app['route.compiler']);
		};
		$app['callback.resolver'] = function()
		{
			return new CallbackResolver();
		};
		$app['event.dispatcher.factory'] = $app->factory(function()
		{
			return new EventDispatcher();
		});
		$app['event.dispatcher'] = function($app)
		{
			$dispatcher = $app['event.dispatcher.factory'];
			$dispatcher->addSubscriber(new ControllerResolverListener($app['callback.resolver'], $app));
			$dispatcher->addSubscriber(new RouteListener($app['route.matcher'], $app['routes']));
			$dispatcher->addSubscriber(new MiddlewareListener($app['callback.resolver'], $app));
			$dispatcher->addSubscriber(new ConverterListener($app['callback.resolver']));
			$dispatcher->addSubscriber(new StringToResponseListener());
			$dispatcher->addSubscriber(new ResourceToResponseListener());
			return $dispatcher;
		};
		$app['request.handler'] = $app->factory(function($app)
		{
			return new RequestHandler($app['event.dispatcher']);
		});
		$app['destination.response.body.factory'] = $app->factory(function()
		{
			return new ResponseBody(fopen('php://output', 'wb'));
		});
		$app['use_forwarded_host'] = false;
		$app['use_x_http_method_override'] = false;
		$app['default.request.factory'] = $app->factory(function($app)
		{
			$headers = [];
			foreach ($_SERVER as $key=>$value)
			{
				if (substr($key, 0, 5) != 'HTTP_') continue;
				$h = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
				$headers[$h] = $value;
			}
			$method = strtoupper($_SERVER['REQUEST_METHOD']);
			$input = [];
			if (in_array($method, ['PUT', 'DELETE', 'PATCH']))
			{
				$input = parse_str(file_get_contents('php://input'));
			}
			else
			{
				$input = $_POST;
			}
			if ($app['use_x_http_method_override'])
			{
				if (!empty($headers['X-Http-Method-Override']))
				{
					$m = $headers['X-Http-Method-Override'];
				}
				elseif (!empty($input['X-Http-Method-Override']))
				{
					$m = $input['X-Http-Method-Override'];
				}
				if (in_array($m, ['GET', 'HEAD','POST','PUT','DELETE','TRACE','OPTIONS','CONNECT','PATCH']))
				{
					$method = $m;
				}
			}
			//create url
			$schema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && $_SERVER['SERVER_PORT'] == '443')? 'https':'http';
			$host = ($app['use_forwarded_host'] && isset($_SERVER['HTTP_X_FORWARDED_HOST']))?  $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset( $_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST']:null);
			$host = isset($host)? $host : $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
			$url = $schema . '://' . $host . $_SERVER['REQUEST_URI'];

			return new Request(
				$method,
				$url,
				[
					'query'=>$_GET,
					'input'=>$input,
					'headers'=>$headers,
					'cookies'=>$_COOKIE,
					'server'=>$_SERVER,
					'files'=>UploadedFile::getNormalizedFiles($_FILES),
					'arguments'=>[]
				],
				RequestInfoInterface::MASTER_REQUEST
			);
		});
		foreach ($defaults as $key=>$value)
		{
			$this[$key] = $value;
		}
	}

	/**
	*	Add a listener for event
	*	@param string $eventName The event name
	*	@param callable $callback The callback
	*	@param int $priority Optional priority of event listener
	*/
	public function listen($eventName, callable $callback, $priority = 10)
	{
		$this['event.dispatcher']->addListener($eventName, $callback, $priority);
	}

	/**
	*	Dispatch a event
	*	@param EventInterface $event The event object
	*/
	public function speak(EventInterface $ev)
	{
		return $this['event.dispatcher']->dispatch($ev);
	}

	/**
	*	Add a callback for "before" middleware (KernelEvents::REQUEST event)
	*	@param mixed $action The The callback
	*	@param int $priority Optional priority of event listener
	*/
	public function before($action, $priority = 10)
	{
		$app = $this;
		$app['event.dispatcher']->addListener(KernelEvents::REQUEST, function($ev) use ($action, $app)
		{
			if ($ev->getRequest()->getType() == RequestInfoInterface::MASTER_REQUEST)
			{
				$action = $app['callback.resolver']->resolve($action);
				$result = call_user_func($action, $ev->getRequest(), $app);
				if ($result instanceof SenderWithDestinationInterface)
				{
					$ev->setResponse($result);
					$ev->stopPropagation();
				}
			}
		}
		, $priority);
	}

	/**
	*	Add a callback for "after" middleware (KernelEvents::RESPONSE event)
	*	@param mixed $action The The callback
	*	@param int $priority Optional priority of event listener
	*/
	public function after($action, $priority = 10)
	{
		$app = $this;
		$app['event.dispatcher']->addListener(KernelEvents::RESPONSE, function($ev) use ($action, $app)
		{
			if ($ev->getRequest()->getType() == RequestInfoInterface::MASTER_REQUEST)
			{
				$action = $app['callback.resolver']->resolve($action);
				call_user_func($action, $ev->getRequest(), $ev->getResponse(), $app);
			}
		}
		, $priority);
	}

	/**
	*	Add a callback for "end" middleware (KernelEvents::TERMINATE event)
	*	@param mixed $action The The callback
	*	@param int $priority Optional priority of event listener
	*/
	public function end($action, $priority = 10)
	{
		$app = $this;
		$app['event.dispatcher']->addListener(KernelEvents::TERMINATE, function($ev) use ($action, $app)
		{
			if ($ev->getRequest()->getType() == RequestInfoInterface::MASTER_REQUEST)
			{
				$action = $app['callback.resolver']->resolve($action);
				call_user_func($action, $ev->getRequest(), $ev->getResponse(), $app);
			}
		}
		, $priority);
	}

	/**
	*	Add a callback for "error" middleware (KernelEvents::ERROR event)
	*	@param mixed $action The The callback
	*	@param int $priority Optional priority of event listener
	*/
	public function error($action, $priority = 10)
	{
		$app = $this;
		$app['event.dispatcher']->addListener(KernelEvents::ERROR, function($ev) use ($action, $app)
		{
			if ($ev->getRequest()->getType() == RequestInfoInterface::MASTER_REQUEST)
			{
				$action = $app['callback.resolver']->resolve($action);
				$r = $this->getReflector($action);
				if (!empty($r->getParameters()) &&
					$r->getParameters()[0]->getClass() &&
					$r->getParameters()[0]->getClass()->isInstance($ev->getException())
				   )
				{
					$result = call_user_func($action, $ev->getException(), $ev->getRequest(), $app);
					if ($result instanceof SenderWithDestinationInterface)
					{
						$ev->setResponse($result);
						$ev->stopPropagation();
					}
				}
			}
		}
		, $priority);
	}

	/**
	*	Add a callback for "view" middleware (KernelEvents::VIEW event)
	*	@param mixed $action The The callback
	*	@param int $priority Optional priority of event listener
	*/
	public function view($action, $priority = 10)
	{
		$app = $this;
		$app['event.dispatcher']->addListener(KernelEvents::VIEW, function($ev) use ($action, $app)
		{
			$action = $app['callback.resolver']->resolve($action);
			$result = call_user_func($action, $ev->getContent(), $ev->getRequest(), $app);
			if ($result instanceof SenderWithDestinationInterface)
			{
				$ev->setResponse($result);
				$ev->stopPropagation();
			}
			elseif (!empty($result))
			{
				$ev->setContent($result);
			}
		}
		, $priority);
	}

	/**
	*	Abort application
	*	@param string $message The message for exception
	*	@param int $code The optional exception code
	*	@throws AbortApplicationException
	*/
	public function abort($message = 'Application is aborted', $code = 500)
	{
		throw new AbortApplicationException($message, $code);
	}

	/**
	*	@param string $path
	*	@param mixed $action
	*	@return RouteInterface
	*/
	public function match($path, $action)
	{
		return $this['routes']->match($path, $action);
	}

	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function get($path, $action)
	{
		return $this['routes']->get($path, $action);
	}

	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function head($path, $action)
	{
		return $this['routes']->head($path, $action);
	}

	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function post($path, $action)
	{
		return $this['routes']->post($path, $action);
	}
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function put($path, $action)
	{
		return $this['routes']->put($path, $action);
	}
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function delete($path, $action)
	{
		return $this['routes']->delete($path, $action);
	}
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function trace($path, $action)
	{
		return $this['routes']->trace($path, $action);
	}
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function options($path, $action)
	{
		return $this['routes']->options($path, $action);
	}
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function connect($path, $action)
	{
		return $this['routes']->connect($path, $action);
	}
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function patch($path, $action)
	{
		return $this['routes']->patch($path, $action);
	}
	
	/**
	*	@param string $path
	*	@param mixed $action
	*	@return Arrow\Contracts\Routing\RouteInterface
	*/
	public function any($path, $action)
	{
		return $this['routes']->any($path, $action);
	}

	/**
	*	@param string $prefix The prefix of URL
	*	@param RouteCollectionInterface $collection Routes for union
	*/
	public function join($prefix = '', RouteCollectionInterface $collection)
	{
		$this['routes']->join($prefix, $collection);
	}

	/**
	*	Register a service
	*	@param ServiceProviderInterface $service The service
	*	@param mixed[] $conf Optioal options for the service
	*/
	public function register(ServiceProviderInterface $service, array $conf = [])
	{
		foreach ($conf as $key=>$value)
		{
			$this[$key] = $value;
		}
		$service->register($this);
	}

	/**
	*	Boot the services
	*/
	public function boot()
	{
		foreach ($this->providers as $provider)
		{
			$provider->boot($this);
		}
	}

	/**
	*	Run application
	*	@param RequestInfoInterface $request Optional request object
	*/
	public function run(RequestInfoInterface $request = null)
	{
		if (self::$booted == false)
		{
			$this->boot();
			self::$booted = true;
		}
		if (self::$runing == false)
		{
			self::$runing = true;
		}
		else
		{
			return;
		}
		if ($request == null) $request = $this['default.request.factory'];
		$handler = $this['request.handler'];
		$response = $handler->handle($request);
		$destination = $this['destination.response.body.factory'];
		$response->send($destination);
		$handler->terminate($request, $response);
	}
}