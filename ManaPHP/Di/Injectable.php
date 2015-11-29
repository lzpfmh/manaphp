<?php 

namespace ManaPHP\Di {

	use ManaPHP\Di;
	use \ManaPHP\Events\EventsAwareInterface;

	/**
	 * ManaPHP\Di\Injectable
	 *
	 * @property \ManaPHP\Mvc\Dispatcher|\ManaPHP\Mvc\DispatcherInterface $dispatcher;
	 * @property \ManaPHP\Mvc\Router|\ManaPHP\Mvc\RouterInterface $router
	 * @property \ManaPHP\Mvc\Url|\ManaPHP\Mvc\UrlInterface $url
	 * @property \ManaPHP\Http\Request|\ManaPHP\Http\RequestInterface $request
	 * @property \ManaPHP\Http\Response|\ManaPHP\Http\ResponseInterface $response
	 * @property \ManaPHP\Http\Response\Cookies|\ManaPHP\Http\Response\CookiesInterface $cookies
	 * @property \ManaPHP\Filter|\ManaPHP\FilterInterface $filter
	 * @property \ManaPHP\Flash\Direct $flash
	 * @property \ManaPHP\Flash\Session $flashSession
	 * @property \ManaPHP\Session\Adapter\Files|\ManaPHP\Session\Adapter|\ManaPHP\Session\AdapterInterface $session
	 * @property \ManaPHP\Events\Manager|\ManaPHP\Events\ManagerInterface $eventsManager
	 * @property \ManaPHP\Db\AdapterInterface $db
	 * @property \ManaPHP\Security $security
	 * @property \ManaPHP\Crypt|\ManaPHP\CryptInterface $crypt
	 * @property \ManaPHP\Escaper|\ManaPHP\EscaperInterface $escaper
	 * @property \ManaPHP\Mvc\Model\Manager|\ManaPHP\Mvc\Model\ManagerInterface $modelsManager
	 * @property \ManaPHP\Mvc\Model\MetaData\Memory|\ManaPHP\Mvc\Model\MetadataInterface $modelsMetadata
	 * @property \ManaPHP\Mvc\Model\Transaction\Manager|\ManaPHP\Mvc\Model\Transaction\ManagerInterface $transactionManager
	 * @property \ManaPHP\Assets\Manager $assets
	 * @property \ManaPHP\Di|\ManaPHP\DiInterface $di
	 * @property \ManaPHP\Session\Bag|\ManaPHP\Session\BagInterface $persistent
	 * @property \ManaPHP\Mvc\View|\ManaPHP\Mvc\ViewInterface $view
	 */
	
	abstract class Injectable implements InjectionAwareInterface, EventsAwareInterface {

		/**
		 * Dependency Injector
		 *
		 * @var \ManaPHP\DiInterface
		 */
		protected $_dependencyInjector=null;

		/**
		 * Events Manager
		 *
		 * @var \ManaPHP\Events\ManagerInterface
		 */
		protected $_eventsManager=null;

		/**
		 * Sets the dependency injector
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @return \ManaPHP\Di\Injectable
		 */
		public function setDI($dependencyInjector){
			$this->_dependencyInjector =$dependencyInjector;
			return $this;
		}


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){
			if(!is_object($this->_dependencyInjector)){
				$this->_dependencyInjector =Di::getDefault();
			}

			return $this->_dependencyInjector;
		}


		/**
		 * Sets the event manager
		 *
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 * @return \ManaPHP\Di\Injectable
		 */
		public function setEventsManager($eventsManager){
			$this->_eventsManager =$eventsManager;
			return $this;
		}


		/**
		 * Returns the internal event manager
		 *
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		public function getEventsManager(){
			return $this->_eventsManager;
		}


		/**
		 * Magic method __get
		 *
		 * @param string $propertyName
		 * @return object
		 * @throws
		 */
		public function __get($propertyName){
			if(!is_object($this->_dependencyInjector)){
				$this->_dependencyInjector =Di::getDefault();
				if(!is_object($this->_dependencyInjector)){
					throw new Exception("A dependency injection object is required to access the application services");
				}
			}

			if($this->_dependencyInjector->has($propertyName)){
				return $this->{$propertyName} =$this->_dependencyInjector->getShared($propertyName);
			}

			if($propertyName ==='di'){
				return $this->{'di'}=$this->_dependencyInjector;
			}

			trigger_error("Access to undefined property " . $propertyName);

			return null;
		}
	}
}
