<?php 

namespace ManaPHP\DI {

	/**
	 * ManaPHP\DI\Injectable
	 *
	 * This class allows to access services in the services container by just only accessing a public property
	 * with the same name of a registered service
	 */
	
	abstract class Injectable implements \ManaPHP\DI\InjectionAwareInterface, \ManaPHP\Events\EventsAwareInterface {

		protected $_dependencyInjector;

		protected $_eventsManager;

		/**
 		 * @var \ManaPHP\Mvc\Dispatcher|\ManaPHP\Mvc\DispatcherInterface
 		 */
		public $dispatcher;

		/**
 		 * @var \ManaPHP\Mvc\Router|\ManaPHP\Mvc\RouterInterface
 		 */
		public $router;

		/**
 		 * @var \ManaPHP\Mvc\Url|\ManaPHP\Mvc\UrlInterface
 		 */
		public $url;

		/**
 		 * @var \ManaPHP\Http\Request|\ManaPHP\HTTP\RequestInterface
 		 */
		public $request;

		/**
 		 * @var \ManaPHP\Http\Response|\ManaPHP\HTTP\ResponseInterface
 		 */
		public $response;

		/**
 		 * @var \ManaPHP\Http\Response\Cookies|\ManaPHP\Http\Response\CookiesInterface
 		 */
		public $cookies;

		/**
 		 * @var \ManaPHP\Filter|\ManaPHP\FilterInterface
 		 */
		public $filter;

		/**
 		 * @var \ManaPHP\Flash\Direct
 		 */
		public $flash;

		/**
 		 * @var \ManaPHP\Flash\Session
 		 */
		public $flashSession;

		/**
 		 * @var \ManaPHP\Session\Adapter\Files|\ManaPHP\Session\Adapter|\ManaPHP\Session\AdapterInterface
 		 */
		public $session;

		/**
 		 * @var \ManaPHP\Events\Manager
 		 */
		public $eventsManager;

		/**
 		 * @var \ManaPHP\Db
 		 */
		public $db;

		/**
 		 * @var \ManaPHP\Security
 		 */
		public $security;

		/**
 		 * @var \ManaPHP\Crypt
 		 */
		public $crypt;

		/**
 		 * @var \ManaPHP\Tag
 		 */
		public $tag;

		/**
 		 * @var \ManaPHP\Escaper|\ManaPHP\EscaperInterface
 		 */
		public $escaper;

		/**
 		 * @var \ManaPHP\Annotations\Adapter\Memory|\ManaPHP\Annotations\Adapter
 		 */
		public $annotations;

		/**
 		 * @var \ManaPHP\Mvc\Model\Manager|\ManaPHP\Mvc\Model\ManagerInterface
 		 */
		public $modelsManager;

		/**
 		 * @var \ManaPHP\Mvc\Model\MetaData\Memory|\ManaPHP\Mvc\Model\MetadataInterface
 		 */
		public $modelsMetadata;

		/**
 		 * @var \ManaPHP\Mvc\Model\Transaction\Manager
 		 */
		public $transactionManager;

		/**
 		 * @var \ManaPHP\Assets\Manager
 		 */
		public $assets;

		/**
		 * @var \ManaPHP\Di|\ManaPHP\DiInterface
	 	 */
		public $di;

		/**
		 * @var \ManaPHP\Session\Bag
	 	 */
		public $persistent;

		/**
 		 * @var \ManaPHP\Mvc\View|\ManaPHP\Mvc\ViewInterface
 		 */
		public $view;
		
		/**
		 * Sets the dependency injector
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @throw \ManaPHP\Di\Exception
		 */
		public function setDI($dependencyInjector){ }


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){ }


		/**
		 * Sets the event manager
		 *
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 */
		public function setEventsManager($eventsManager){ }


		/**
		 * Returns the internal event manager
		 *
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		public function getEventsManager(){ }


		/**
		 * Magic method __get
		 *
		 * @param string $propertyName
		 */
		public function __get($property){ }

	}
}
