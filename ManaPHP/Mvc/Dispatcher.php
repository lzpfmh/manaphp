<?php 

namespace ManaPHP\Mvc {

	use ManaPHP\Di\InjectionAware;
	use ManaPHP\Di\InjectionAwareInterface;
	use ManaPHP\Events\EventsAware;
	use ManaPHP\Events\EventsAwareInterface;
	use ManaPHP\Mvc\Dispatcher\Exception;

	/**
	 * ManaPHP\Mvc\Dispatcher
	 *
	 * Dispatching is the process of taking the request object, extracting the module name,
	 * controller name, action name, and optional parameters contained in it, and then
	 * instantiating a controller and calling an action of that controller.
	 *
	 *<code>
	 *
	 *	$di = new ManaPHP\Di();
	 *
	 *	$dispatcher = new ManaPHP\Mvc\Dispatcher();
	 *
	 *  $dispatcher->setDI($di);
	 *
	 *	$dispatcher->setControllerName('posts');
	 *	$dispatcher->setActionName('index');
	 *	$dispatcher->setParams(array());
	 *
	 *	$controller = $dispatcher->dispatch();
	 *
	 *</code>
	 */
	
	class Dispatcher implements DispatcherInterface, InjectionAwareInterface, EventsAwareInterface{
		use EventsAware,InjectionAware;

		const EXCEPTION_NO_DI = 0;

		const EXCEPTION_CYCLIC_ROUTING = 1;

		const EXCEPTION_CONTROLLER_NOT_FOUND = 2;

		const EXCEPTION_INVALID_CONTROLLER = 3;

		const EXCEPTION_INVALID_PARAMS = 4;

		const EXCEPTION_ACTION_NOT_FOUND = 5;


		protected $_activeController;

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
		protected $_controllerName;

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

		protected $_lastController;

		/**
		 * @var string
		 */
		protected $_defaultNamespace;

		/**
		 * @var string
		 */
		protected $_defaultController='Index';

		/**
		 * @var string
		 */
		protected $_defaultAction='index';

		/**
		 * @var string
		 */
		protected $_controllerSuffix='Controller';

		/**
		 * @var string
		 */
		protected $_actionSuffix='Action';


		/**
		 * @var string
		 */
		protected $_previousControllerClass;
		/**
		 * @var string
		 */
		protected $_previousControllerName;

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

			if($this->fireEvent('dispatch:beforeDispatchLoop',$this) ===false){
				return false;
			}

			$controller =null;
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

				if($this->fireEvent('dispatch:beforeDispatch',$this) ===false){
					continue;
				}

				if($this->_finished ===false){
					continue;
				}

				$controllerClass = $this->getControllerClass();

				if(!$this->_dependencyInjector->has($controllerClass)&& !class_exists($controllerClass)){
					if($this->_throwDispatchException($controllerClass . ' handler class cannot be loaded', self::EXCEPTION_CONTROLLER_NOT_FOUND) ===false){
						if($this->_finished ===false){
							continue;
						}
					}

					break;
				}

				$controller =$this->_dependencyInjector->getShared($controllerClass);
				$wasFreshInstance =$this->_dependencyInjector->wasFreshInstance();
				if(!is_object($controller)){
					if($this->_throwDispatchException('Invalid handler returned from the services container', self::EXCEPTION_INVALID_CONTROLLER) ===false ){
						if($this->_finished ===false){
							continue;
						}
					}

					break;
				}
				$this->_activeController =$controller;

				$actionMethod =$this->_actionName .$this->_actionSuffix;
				if(!method_exists($controller, $actionMethod)){
					if($this->fireEvent('dispatch:beforeNotFoundAction', $this) ===false){
						continue;
					}

					if($this->_finished ===false){
						continue;
					}


					if($this->_throwDispatchException('Action \'' . $this->_actionName . '\' was not found on handler \'' . $controllerClass . '\'', self::EXCEPTION_ACTION_NOT_FOUND) ===false){
						if($this->_finished ===false){
							continue;
						}
					}

					break;
				}

				// Calling beforeExecuteRoute as callback
				if(method_exists($controller, 'beforeExecuteRoute')){
					if($controller->beforeExecuteRoute($this) ===false){
						continue;
					}

					if($this->_finished ===false){
						continue;
					}
				}

				if($wasFreshInstance) {
					if (method_exists($controller, 'initialize')) {
						$controller->initialize();
					}
				}

				try{
					$this->_returnedValue =call_user_func_array([$controller,$actionMethod],$this->_params);
					$this->_lastController =$controller;
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

				// Call afterDispatch
				$this->fireEvent('dispatch:afterDispatch', $this);

				if(method_exists($controller,'afterExecuteRoute')){
					if($controller->afterExecuteRoute($this,$value) ===false) {
						continue;
					}

					if($this->_finished ===false){
						continue;
					}
				}
			}

			$this->fireEvent('dispatch:afterDispatchLoop',$this);

			return $controller;
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

			$this->_previousControllerClass=$this->getControllerClass();

			if(isset($forward['namespace'])){
				$this->_namespaceName =$forward['namespace'];
			}

			if(isset($forward['controller'])){
				$this->_previousControllerName =$this->_controllerName;
				$this->_controllerName =$forward['controller'];
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
		public function getControllerClass(){
			$this->_resolveEmptyProperties();

			if(strpos($this->_controllerName,'\\') ===false){
				$camelizedClass=$this->_camelize($this->_controllerName);
			}else{
				$camelizedClass =$this->_controllerName;
			}

			if($this->_namespaceName){
				$handlerClass =rtrim($this->_namespaceName,'\\').'\\'.$camelizedClass.$this->_controllerSuffix;
			}else{
				$handlerClass =$camelizedClass.$this->_controllerSuffix;
			}

			return $handlerClass;
		}

		/**
		 * Set empty properties to their defaults (where defaults are available)
		 */
		protected function _resolveEmptyProperties(){
			// If the current namespace is null we used the set in this->_defaultNamespace
			if ($this->_namespaceName ===null) {
				$this->_namespaceName = $this->_defaultNamespace;
			}

			// If the handler is null we use the set in this->_defaultHandler
			if ($this->_controllerName ===null) {
				$this->_controllerName = $this->_defaultController;
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
			$this->_controllerSuffix =$controllerSuffix;
		}


		/**
		 * Sets the default controller name
		 *
		 * @param string $controllerName
		 */
		public function setDefaultController($controllerName){
			$this->_defaultController =$controllerName;
		}


		/**
		 * Sets the controller name to be dispatched
		 *
		 * @param string $controllerName
		 */
		public function setControllerName($controllerName){
			$this->_controllerName =$controllerName;
		}


		/**
		 * Gets last dispatched controller name
		 *
		 * @return string
		 */
		public function getControllerName(){
			return $this->_controllerName;
		}

		/**
		 * Returns the previous controller class in the dispatcher
		 *
		 * @return string
		 */
		public function getPreviousControllerClass(){
			return $this->_previousControllerClass;
		}

		/**
		 * Returns the previous controller in the dispatcher
		 *
		 * @return string
		 */
		public function getPreviousControllerName(){
			return $this->_previousControllerName;
		}

		/**
		 * Returns the previous action in the dispatcher
		 *
		 * @return string
		 */
		public function getPreviousActionName(){
			return $this->_previousActionName;
		}

		/**
		 * Throws an internal exception
		 *
		 * @param string $message
		 * @param int $exceptionCode
		 * @return boolean
		 * @throws \ManaPHP\Mvc\Dispatcher\Exception
		 */
		protected function _throwDispatchException($message, $exceptionCode=0){
			if(!is_object($this->_dependencyInjector)){
				throw new Exception(
					"A dependency injection container is required to access the 'response' service",
					\ManaPHP\Dispatcher::EXCEPTION_NO_DI
				);
			}

			$response =$this->_dependencyInjector->getShared('response');
			$response->setStatusCode(404, 'Not Found');

			$exception =new Exception($message, $exceptionCode);

			if($this->_handleException($exception) ===false){
				return false;
			}

			throw $exception;
		}


		/**
		 * Handles a user exception
		 *
		 * @param \Exception $exception
		 * @return boolean
		 *
		 * @warning If any additional logic is to be implemented here, please check
		 * ManaPHP_dispatcher_fire_event() first
		 */
		protected function _handleException($exception){

		}
	}
}
