<?php 

namespace ManaPHP\Di {

	use ManaPHP\Di;
	use ManaPHP\Events\EventsAware;
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
	 * @property \ManaPHP\Http\SessionInterface $session
	 * @property \ManaPHP\Events\ManagerInterface $eventsManager
	 * @property \ManaPHP\DbInterface $db
	 //* @property \ManaPHP\Security $security
	 //* @property \ManaPHP\CryptInterface $crypt
	 //* @property \ManaPHP\EscaperInterface $escaper
	 * @property \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
	 * @property \ManaPHP\Mvc\Model\MetadataInterface $modelsMetadata
	 //* @property \ManaPHP\Mvc\Model\Transaction\ManagerInterface $transactionManager
	 //* @property \ManaPHP\Assets\Manager $assets
	 * @property \ManaPHP\Di|\ManaPHP\DiInterface $di
	 //* @property \ManaPHP\Session\BagInterface $persistent
	 * @property \ManaPHP\Mvc\ViewInterface $view
	 */
	
	abstract class Injectable implements InjectionAwareInterface, EventsAwareInterface {
		use EventsAware,InjectionAware;
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
