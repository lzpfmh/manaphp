<?php 

namespace ManaPHP\DI {

	/**
	 * ManaPHP\DI\Service
	 *
	 * Represents individually a service in the services container
	 *
	 *<code>
	 * $service = new ManaPHP\DI\Service('request', 'ManaPHP\Http\Request');
	 * $request = $service->resolve();
	 *<code>
	 *
	 */
	
	class Service implements \ManaPHP\DI\ServiceInterface {

		/**
		 * \ManaPHP\DI\Service
		 *
		 * @param string $name
		 * @param mixed $definition
		 * @param boolean $shared
		 */
		public function __construct($name, $definition, $shared=null){ }


		/**
		 * Returns the service's name
		 *
		 * @param string
		 * @return string
		 */
		public function getName(){ }


		/**
		 * Sets if the service is shared or not
		 *
		 * @param boolean $shared
		 */
		public function setShared($shared){ }


		/**
		 * Check whether the service is shared or not
		 *
		 * @return boolean
		 */
		public function isShared(){ }


		/**
		 * Sets/Resets the shared instance related to the service
		 *
		 * @param mixed $sharedInstance
		 */
		public function setSharedInstance($sharedInstance){ }


		/**
		 * Set the service definition
		 *
		 * @param mixed $definition
		 */
		public function setDefinition($definition){ }


		/**
		 * Returns the service definition
		 *
		 * @return mixed
		 */
		public function getDefinition(){ }


		/**
		 * Resolves the service
		 *
		 * @param array $parameters
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @return object
		 */
		public function resolve($parameters=null, $dependencyInjector=null){ }


		/**
		 * Changes a parameter in the definition without resolve the service
		 *
		 * @param int $position
		 * @param array $parameter
		 * @return \ManaPHP\DI\Service
		 */
		public function setParameter($position, $parameter){ }


		/**
		 * Returns a parameter in a specific position
		 *
		 * @param int $position
		 * @return array
		 */
		public function getParameter($position){ }


		/**
		 * Returns true if the service was resolved
		 *
		 * @return bool
		 */
		public function isResolved(){ }


		/**
		 * Restore the internal state of a service
		 *
		 * @param array $attributes
		 * @return \ManaPHP\DI\Service
		 */
		public static function __set_state($properties=null){ }

	}
}
