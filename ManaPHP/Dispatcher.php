<?php 

namespace ManaPHP {
	use ManaPHP\Di\InjectionAwareInterface;
	use ManaPHP\Events\EventsAwareInterface;

	/**
	 * ManaPHP\Dispatcher
	 *
	 * This is the base class for ManaPHP\Mvc\Dispatcher and ManaPHP\CLI\Dispatcher.
	 * This class can't be instantiated directly, you can use it to create your own dispatchers
	 */
	
	abstract class Dispatcher implements DispatcherInterface, InjectionAwareInterface, EventsAwareInterface{

		const EXCEPTION_NO_DI = 0;

		const EXCEPTION_CYCLIC_ROUTING = 1;

		const EXCEPTION_HANDLER_NOT_FOUND = 2;

		const EXCEPTION_INVALID_HANDLER = 3;

		const EXCEPTION_INVALID_PARAMS = 4;

		const EXCEPTION_ACTION_NOT_FOUND = 5;

		/**
		 * @var \ManaPHP\DiInterface
		 */
		protected $_dependencyInjector;

		/**
		 * @var \ManaPHP\Events\ManagerInterface
		 */
		protected $_eventsManager;

		protected $_activeHandler;

		/**
		 * @var boolean
		 */
		protected $_finished=false;

		/**
		 * @var boolean
		 */
		protected $_forwarded=false;

		/**
		 * @var string
		 */
		protected $_moduleName;

		/**
		 * @var string
		 */
		protected $_namespaceName;

		/**
		 * @var string
		 */
		protected $_handlerName;

		/**
		 * @var string
		 */
		protected $_actionName;

		/**
		 * @var array
		 */
		protected $_params=[];

		/**
		 * @var mixed
		 */
		protected $_returnedValue;

		protected $_lastHandler;

		/**
		 * @var string
		 */
		protected $_defaultNamespace;

		protected $_defaultHandler;

		/**
		 * @var string
		 */
		protected $_defaultAction='';

		/**
		 * @var string
		 */
		protected $_handlerSuffix='';

		/**
		 * @var string
		 */
		protected $_actionSuffix='Action';

		/**
		 * @var boolean
		 */
		protected $_isExactHandler;

		/**
		 * @var string
		 */
		protected $_previousHandlerName;

		/**
		 * @var string
		 */
		protected $_previousActionName;

		/**
		 * \ManaPHP\Dispatcher constructor
		 */
		public function __construct(){
		}

		/**
		 * Sets the dependency injector
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector){
			$this->_dependencyInjector =$dependencyInjector;
		}


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){
			return $this->_dependencyInjector;
		}

		/**
		 * Sets the events manager
		 * @param \ManaPHP\Events\EventsAwareInterface $eventsManager
		 */
		public function setEventsManager($eventsManager){
			$this->_eventsManager =$eventsManager;
		}

		/**
		 * Returns the internal event manager
		 * @return \ManaPHP\Events\EventsAwareInterface
		 */
		public function getEventsManager(){
			return $this->_eventsManager;
		}

		/**
		 * Sets the default action suffix
		 *
		 * @param string $actionSuffix
		 * @return static
		 */
		public function setActionSuffix($actionSuffix){
			$this->_actionSuffix =$actionSuffix;
			return $this;
		}


		/**
		 * Sets the module where the controller is (only informative)
		 *
		 * @param string $moduleName
		 * @return static
		 */
		public function setModuleName($moduleName){
			$this->_moduleName =$moduleName;
			return $this;
		}


		/**
		 * Gets the module where the controller class is
		 *
		 * @return string
		 */
		public function getModuleName(){
			return $this->_moduleName;
		}


		/**
		 * Sets the namespace where the controller class is
		 *
		 * @param string $namespaceName
		 * @return static
		 */
		public function setNamespaceName($namespaceName){
			$this->_namespaceName =$namespaceName;
			return $this;
		}


		/**
		 * Gets a namespace to be prepended to the current handler name
		 *
		 * @return string
		 */
		public function getNamespaceName(){
			return $this->_namespaceName;
		}


		/**
		 * Sets the default namespace
		 *
		 * @param string $namespace
		 * @return static
		 */
		public function setDefaultNamespace($namespace){
			$this->_defaultNamespace =$namespace;
			return $this;
		}


		/**
		 * Returns the default namespace
		 *
		 * @return string
		 */
		public function getDefaultNamespace(){
			return $this->_defaultNamespace;
		}


		/**
		 * Sets the default action name
		 *
		 * @param string $actionName
		 * @return static
		 */
		public function setDefaultAction($actionName){
			$this->_defaultAction=$actionName;
			return $this;
		}


		/**
		 * Sets the action name to be dispatched
		 *
		 * @param string $actionName
		 * @return static
		 */
		public function setActionName($actionName){
			$this->_actionName =$actionName;
			return $this;
		}


		/**
		 * Gets the latest dispatched action name
		 *
		 * @return string
		 */
		public function getActionName(){
			return $this->_actionName;
		}


		/**
		 * Sets action params to be dispatched
		 *
		 * @param array $params
		 * @return static
		 */
		public function setParams($params){
			if(!is_array($params)){
				$this->_throwDispatchException('Parameters must be an Array');
			}
			$this->_params=$params;

			return $this;
		}


		/**
		 * Gets action params
		 *
		 * @return array
		 */
		public function getParams(){
			return $this->_params;
		}


		/**
		 * Set a param by its name or numeric index
		 *
		 * @param  string|int $param
		 * @param  mixed $value
		 * @return static
		 */
		public function setParam($param, $value){
			$this->_params[$param]=$value;
			return $this;
		}


		/**
		 * Gets a param by its name or numeric index
		 *
		 * @param  string|int $param
		 * @param  string|array $filters
		 * @param  mixed $defaultValue
		 * @return mixed
		 */
		public function getParam($param, $filters=null, $defaultValue=null){
			if(!isset($this->_params[$param])){
				return $defaultValue;
			}

			if($filters ===null){
				return $this->_params[$param];
			}

			if(!is_object($this->_dependencyInjector)){
				$this->_throwDispatchException("A dependency injection object is required to access the 'filter' service", self::EXCEPTION_NO_DI);
			}

			return $this->_dependencyInjector->getShared('filter')->sanitize($this->_params[$param],$filters);
		}


		/**
		 * Sets the latest returned value by an action manually
		 *
		 * @param mixed $value
		 * @return static
		 */
		public function setReturnedValue($value){
			$this->_returnedValue =$value;
			return $this;
		}


		/**
		 * Returns value returned by the latest dispatched action
		 *
		 * @return mixed
		 */
		public function getReturnedValue(){
			return $this->_returnedValue;
		}


		/**
		 * Dispatches a handle action taking into account the routing parameters
		 *
		 * @return object|boolean
		 * @throws
		 */
		public function dispatch(){
			if(!is_object($this->_dependencyInjector)){
				$this->_throwDispatchException('A dependency injection container is required to access related dispatching services', self::EXCEPTION_NO_DI);
				return false;
			}

			if(is_object($this->_eventsManager)){
				if($this->_eventsManager->fire('dispatch:beforeDispatchLoop',$this) ===false){
					return false;
				}
			}

			$handler =null;
			$numberDispatches =0;
			$this->_finished =false;
			while($this->_finished ===false){
				// if the user made a forward in the listener,the $this->_finished will be changed to false.
				$this->_finished =true;

				if($numberDispatches++ ===256){
					$this->_throwDispatchException('Dispatcher has detected a cyclic routing causing stability problems', self::EXCEPTION_CYCLIC_ROUTING);
					break;
				}

				$this->_resolveEmptyProperties();

				if(is_object($this->_eventsManager)){
					if($this->_eventsManager->fire('dispatch:beforeDispatch',$this) ===false){
						continue;
					}

					if($this->_finished ===false){
						continue;
					}
				}

				$handlerClass = $this->getHandlerClass();

				if(!$this->_dependencyInjector->has($handlerClass)&& !class_exists($handlerClass)){
					if($this->_throwDispatchException($handlerClass . ' handler class cannot be loaded', self::EXCEPTION_HANDLER_NOT_FOUND) ===false){
						if($this->_finished ===false){
							continue;
						}
					}

					break;
				}

				$handler =$this->_dependencyInjector->getShared($handlerClass);
				$wasFreshInstance =$this->_dependencyInjector->wasFreshInstance();
				if(!is_object($handler)){
					if($this->_throwDispatchException('Invalid handler returned from the services container', self::EXCEPTION_INVALID_HANDLER) ===false ){
						if($this->_finished ===false){
							continue;
						}
					}

					break;
				}
				$this->_activeHandler =$handler;

				$actionMethod =$this->_actionName .$this->_actionSuffix;
				if(!method_exists($handler, $actionMethod)){
					if(is_object($this->_eventsManager)){
						if($this->_eventsManager->fire('dispatch:beforeNotFoundAction', $this) ===false){
							continue;
						}

						if($this->_finished ===false){
							continue;
						}
					}

					if($this->_throwDispatchException('Action \'' . $this->_actionName . '\' was not found on handler \'' . $handlerClass . '\'', self::EXCEPTION_ACTION_NOT_FOUND) ===false){
						if($this->_finished ===false){
							continue;
						}
					}

					break;
				}

				// Calling beforeExecuteRoute as callback
				if(method_exists($handler, 'beforeExecuteRoute')){
					if($handler->beforeExecuteRoute($this) ===false){
						continue;
					}

					if($this->_finished ===false){
						continue;
					}
				}

				if($wasFreshInstance) {
					if (method_exists($handler, 'initialize')) {
						$handler->initialize();
					}
				}

				try{
					$this->_returnedValue =call_user_func_array([$handler,$actionMethod],$this->_params);
					$this->_lastHandler =$handler;
				}catch (\Exception $e){
					if($this->_handleException($e) ===false){
						if($this->_finished ===false){
							continue;
						}
					} else{
						throw $e;
					}
				}

				$value=null;
				if(is_object($this->_eventsManager)){
					// Call afterDispatch
					$this->_eventsManager->fire('dispatch:afterDispatch', $this);
				}

				if(method_exists($handler,'afterExecuteRoute')){
					if($handler->afterExecuteRoute($this,$value) ===false) {
						continue;
					}

					if($this->_finished ===false){
						continue;
					}
				}
			}

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('dispatch:afterDispatchLoop',$this);
			}

			return $handler;
		}


		/**
		 * Forwards the execution flow to another controller/action
		 * Dispatchers are unique per module. Forwarding between modules is not allowed
		 *
		 *<code>
		 *  $this->dispatcher->forward(array('controller' => 'posts', 'action' => 'index'));
		 *</code>
		 *
		 * @param array $forward
		 */
		public function forward($forward){
			if(!is_array($forward)){
				$this->_throwDispatchException('Forward parameter must be an Array');
				return ;
			}

			if(isset($forward['namespace'])){
				$this->_namespaceName =$forward['namespace'];
			}

			if(isset($forward['controller'])){
				$this->_previousHandlerName =$this->_handlerName;
				$this->_handlerName =$forward['controller'];
			}else{
				if(isset($forward['task'])){
					$this->_previousHandlerName =$this->_handlerName;
					$this->_handlerName =$forward['task'];
				}
			}

			if(isset($forward['action'])){
				$this->_previousActionName =$this->_actionName;
				$this->_actionName =$forward['action'];
			}

			if(isset($forward['params'])){
				$this->_params =$forward['params'];
			}

			$this->_finished =false;
			$this->_forwarded =true;
		}


		/**
		 * Check if the current executed action was forwarded by another one
		 *
		 * @return boolean
		 */
		public function wasForwarded(){
			return $this->_forwarded;
		}


		/**
		 * @param string $str
		 * @return string
		 */
		protected function _camelize($str){
			$parts =explode('_',$str);
			foreach($parts as $k=>$v){
				$parts[$k]=ucfirst($v);
			}

			return implode('',$parts);
		}

		/**
		 * Possible class name that will be located to dispatch the request
		 *
		 * @return string
		 */
		public function getHandlerClass(){
			$this->_resolveEmptyProperties();

			if(strpos($this->_handlerName,'\\') ===false){
				$camelizedClass=$this->_camelize($this->_handlerName);
			}else{
				$camelizedClass =$this->_handlerName;
			}

			if($this->_namespaceName){
				$handlerClass =rtrim($this->_namespaceName,'\\').'\\'.$camelizedClass.$this->_handlerSuffix;
			}else{
				$handlerClass =$camelizedClass.$this->_handlerSuffix;
			}

			return $handlerClass;
		}

		/**Throws an internal exception
		 *
		 * @param string $message
		 * @param int $exceptionCode
		 * @return boolean
		 */
		abstract protected function _throwDispatchException($message, $exceptionCode=0);

		/**
		 * Handles a user exception
		 *
		 * @param \Exception $exception
		 * @return boolean
		 */
		abstract protected function _handleException($exception);


		/**
		 * Set empty properties to their defaults (where defaults are available)
		 */
		protected function _resolveEmptyProperties(){
			// If the current namespace is null we used the set in this->_defaultNamespace
			if ($this->_namespaceName ===null) {
				$this->_namespaceName = $this->_defaultNamespace;
			}

			// If the handler is null we use the set in this->_defaultHandler
			if ($this->_handlerName ===null) {
				$this->_handlerName = $this->_defaultHandler;
			}

			// If the action is null we use the set in this->_defaultAction
			if ($this->_actionName ===null) {
				$this->_actionName = $this->_defaultAction;
			}
		}

		/**
		 * Sets the default controller suffix
		 *
		 * @param string $controllerSuffix
		 */
		public function setControllerSuffix($controllerSuffix){
			$this->_handlerSuffix =$controllerSuffix;
		}


		/**
		 * Sets the default controller name
		 *
		 * @param string $controllerName
		 */
		public function setDefaultController($controllerName){
			$this->_defaultHandler =$controllerName;
		}


		/**
		 * Sets the controller name to be dispatched
		 *
		 * @param string $controllerName
		 */
		public function setControllerName($controllerName){
			$this->_handlerName =$controllerName;
		}


		/**
		 * Gets last dispatched controller name
		 *
		 * @return string
		 */
		public function getControllerName(){
			return $this->_handlerName;
		}


		/**
		 * Returns the previous controller in the dispatcher
		 *
		 * @return string
		 */
		public function getPreviousControllerName(){
			return $this->_previousHandlerName;
		}

		/**
		 * Returns the previous action in the dispatcher
		 *
		 * @return string
		 */
		public function getPreviousActionName(){
			return $this->_previousActionName;
		}
	}
}
