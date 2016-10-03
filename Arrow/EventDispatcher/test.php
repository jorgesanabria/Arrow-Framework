<?php
//esta forma es para la linea de comandos
/*require '/home/jorge/Arrow/Contracts/EventDispatcher/EventDispatcherInterface.php';
require '/home/jorge/Arrow/Contracts/EventDispatcher/EventInterface.php';
require '/home/jorge/Arrow/Contracts/EventDispatcher/SubscriberInterface.php';
require 'abstractEvent.php';
require 'EventDispatcher.php';
*/
//esta forma solo funciona en servidores
require '../Contracts/EventDispatcher/EventDispatcherInterface.php';
require '../Contracts/EventDispatcher/EventInterface.php';
require '../Contracts/EventDispatcher/SubscriberInterface.php';
require 'abstractEvent.php';
require 'EventDispatcher.php';
use Arrow\EventDispatcher\EventDispatcher;
use Arrow\EventDispatcher\abstractEvent;
use Arrow\Contracts\EventDispatcher\SubscriberInterface;
use Arrow\Contracts\EventDispatcher\EventDispatcherInterface;
class UnEvento extends abstractEvent
{
	protected $msg;
	public function __construct($msg)
	{
		$this->msg = $msg;
	}
	public function getEventName()
	{
		return 'unEvento';
	}
	public function getMessage()
	{
		return $this->msg;
	}
	public function setMessage($msg)
	{
		$this->msg = $msg;
	}
}
$dispatcher = new EventDispatcher();
$dispatcher->addListener('unEvento', function(UnEvento $ev, EventDispatcher $evdispatcher)
{
	$ev->setMessage(1);
}, 8);

$dispatcher->addListener('unEvento', function(UnEvento $ev, EventDispatcher $evdispatcher)
{
	$ev->setMessage($ev->getMessage() + 1);
}, 9);
$listener = function(UnEvento $ev, EventDispatcher $evdispatcher)
{
	$ev->setMessage($ev->getMessage() + 3);
};
$dispatcher->addListener('unEvento', $listener, 100);
$dispatcher->addListener('unEvento', function(UnEvento $ev, EventDispatcher $evdispatcher)
{
	echo $ev->getMessage();
}, 200);
$dispatcher->dispatch(new UnEvento(''));
echo '<pre>';
//print_r($dispatcher);

$dispatcher->removeListener('unEvento', $listener);
$subscriber = new class() implements SubscriberInterface
{
	public function subscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->addListener('unEvento', [$this, 'onEvento']);
	}
	public function unsubscribe(EventDispatcherInterface $dispatcher)
	{
		$dispatcher->removeListener('unEvento', [$this, 'onEvento']);
	}
	public function onEvento(UnEvento $ev)
	{
		echo 'hola ' . $ev->getMessage();
	}
};
$dispatcher->addSubscriber($subscriber);
$dispatcher->removeSubscriber($subscriber);
$dispatcher->dispatch(new UnEvento('jajajajaja'));
echo '<pre>';