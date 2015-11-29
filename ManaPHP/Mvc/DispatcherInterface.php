<?php 

namespace ManaPHP\Mvc {

	/**
	 * ManaPHP\Mvc\DispatcherInterface initializer
	 */
	
	interface DispatcherInterface extends \ManaPHP\DispatcherInterface {

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
