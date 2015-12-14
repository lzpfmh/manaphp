<?php 

namespace ManaPHP\Mvc {

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
	
	class Dispatcher extends \ManaPHP\Dispatcher implements  DispatcherInterface {

		public function __construct(){
			parent::__construct();

			$this->_controllerSuffix ='Controller';
			$this->_defaultController ='Index';
			$this->_defaultAction='index';
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
