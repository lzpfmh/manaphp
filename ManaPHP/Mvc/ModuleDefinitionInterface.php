<?php 

namespace ManaPHP\Mvc {

	/**
	 * ManaPHP\Mvc\ModuleDefinitionInterface initializer
	 */
	
	interface ModuleDefinitionInterface {

		/**
		 * Registers an autoloader related to the module
		 */
		public function registerAutoloaders();


		/**
		 * Registers services related to the module
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function registerServices($dependencyInjector);

	}
}
