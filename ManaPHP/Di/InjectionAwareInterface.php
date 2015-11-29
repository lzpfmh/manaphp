<?php 

namespace ManaPHP\DI {

	/**
	 * ManaPHP\DI\InjectionAwareInterface initializer
	 */
	
	interface InjectionAwareInterface {

		/**
		 * Sets the dependency injector
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector);


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI();

	}
}
