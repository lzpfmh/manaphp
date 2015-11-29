<?php 

namespace ManaPHP\Mvc {

	/**
	 * ManaPHP\Mvc\ModuleDefinitionInterface initializer
	 */
	
	interface ModuleDefinitionInterface {

		/**
		 * Registers an autoloader related to the module
		 *
		 * * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function registerAutoloaders($dependencyInjector);


		/**
		 * Registers services related to the module
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function registerServices($dependencyInjector);

	}
}
