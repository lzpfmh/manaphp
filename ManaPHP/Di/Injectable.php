<?php 

namespace ManaPHP\Di {

	use ManaPHP\Di;
	use \ManaPHP\Events\EventsAwareInterface;

	/**
	 * ManaPHP\Di\Injectable
	 *
	 * @property \ManaPHP\Mvc\DispatcherInterface $dispatcher;
	 * @property \ManaPHP\Mvc\RouterInterface $router
	// * @property \ManaPHP\Mvc\UrlInterface $url
	 * @property \ManaPHP\Http\RequestInterface $request
	 * @property \ManaPHP\Http\ResponseInterface $response
	 * @property \ManaPHP\Http\Response\CookiesInterface $cookies
	 //* @property \ManaPHP\FilterInterface $filter
	 //* @property \ManaPHP\Flash\Direct $flash
	 //* @property \ManaPHP\Flash\Session $flashSession
	 //* @property \ManaPHP\Session\Adapter\Files|\ManaPHP\Session\Adapter|\ManaPHP\Session\AdapterInterface $session
	 * @property \ManaPHP\Events\ManagerInterface $eventsManager
	 //* @property \ManaPHP\Db\AdapterInterface $db
	 //* @property \ManaPHP\Security $security
	 //* @property \ManaPHP\CryptInterface $crypt
	 //* @property \ManaPHP\EscaperInterface $escaper
	 //* @property \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
	 //* @property \ManaPHP\Mvc\Model\MetadataInterface $modelsMetadata
	 //* @property \ManaPHP\Mvc\Model\Transaction\ManagerInterface $transactionManager
	 //* @property \ManaPHP\Assets\Manager $assets
	 //* @property \ManaPHP\Di|\ManaPHP\DiInterface $di
	 //* @property \ManaPHP\Session\BagInterface $persistent
	 * @property \ManaPHP\Mvc\ViewInterface $view
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
		 */
		public function __get($propertyName){
			if(!is_object($this->_dependencyInjector)){
				$this->_dependencyInjector =Di::getDefault();
			}

			if($this->_dependencyInjector->has($propertyName)){
				return $this->{$propertyName} =$this->_dependencyInjector->getShared($propertyName);
			}

			if($propertyName ==='di'){
				return $this->{'di'}=$this->_dependencyInjector;
			}

			trigger_error('Access to undefined property ' . $propertyName);

			return null;
		}
	}
}
