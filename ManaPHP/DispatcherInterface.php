<?php 

namespace ManaPHP {

	/**
	 * ManaPHP\DispatcherInterface initializer
	 */
	
	interface DispatcherInterface {

		/**
		 * Sets the default action suffix
		 *
		 * @param string $actionSuffix
		 * @return static
		 */
		public function setActionSuffix($actionSuffix);


		/**
		 * Sets the default namespace
		 *
		 * @param string $namespace
		 * @return static
		 */
		public function setDefaultNamespace($namespace);


		/**
		 * Sets the namespace which the controller belongs to
		 * @param string $namespaceName
		 * @return static
		 */
		public function setNamespaceName($namespaceName);

		/**
		 * Sets the module name which the application belongs to
		 * @param string $moduleName
		 * @return static
		 */
		public function setModuleName($moduleName);

		/**
		 * Sets the default action name
		 *
		 * @param string $actionName
		 * @return static
		 */
		public function setDefaultAction($actionName);


		/**
		 * Sets the action name to be dispatched
		 *
		 * @param string $actionName
		 * @return static
		 */
		public function setActionName($actionName);


		/**
		 * Gets last dispatched action name
		 *
		 * @return string
		 */
		public function getActionName();


		/**
		 * Sets action params to be dispatched
		 *
		 * @param array $params
		 * @return static
		 */
		public function setParams($params);


		/**
		 * Gets action params
		 *
		 * @return array
		 */
		public function getParams();


		/**
		 * Set a param by its name or numeric index
		 *
		 * @param  string|int $param
		 * @param  mixed $value
		 * @return static
		 */
		public function setParam($param, $value);


		/**
		 * Gets a param by its name or numeric index
		 *
		 * @param  string|int $param
		 * @param  string|array $filters
		 * @param mixed $defaultValue
		 * @return mixed
		 */
		public function getParam($param, $filters=null,$defaultValue=null);


		/**
		 * Returns value returned by the latest dispatched action
		 *
		 * @return mixed
		 */
		public function getReturnedValue();


		/**
		 * Dispatches a handle action taking into account the routing parameters
		 *
		 * @return \ManaPHP\Mvc\ControllerInterface
		 */
		public function dispatch();


		/**
		 * Forwards the execution flow to another controller/action
		 *
		 * @param array $forward
		 */
		public function forward($forward);

		/**
		 * Check if the current executed action was forwarded by another one
		 *
		 * @return boolean
		 */
		public function wasForwarded();

		/**
		 * Sets the default controller suffix
		 *
		 * @param string $controllerSuffix
		 */
		public function setControllerSuffix($controllerSuffix);


		/**
		 * Sets the default controller name
		 *
		 * @param string $controllerName
		 */
		public function setDefaultController($controllerName);


		/**
		 * Sets the controller name to be dispatched
		 *
		 * @param string $controllerName
		 */
		public function setControllerName($controllerName);


		/**
		 * Gets last dispatched controller name
		 *
		 * @return string
		 */
		public function getControllerName();


		/**
		 * Returns the latest dispatched controller
		 *
		 * @return \ManaPHP\Mvc\ControllerInterface
		 */
		public function getLastController();


		/**
		 * Returns the active controller in the dispatcher
		 *
		 * @return \ManaPHP\Mvc\ControllerInterface
		 */
		public function getActiveController();

	}
}
