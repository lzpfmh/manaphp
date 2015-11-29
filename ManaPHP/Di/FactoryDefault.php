<?php 

namespace ManaPHP\DI {

	/**
	 * ManaPHP\DI\FactoryDefault
	 *
	 * This is a variant of the standard ManaPHP\DI. By default it automatically
	 * registers all the services provided by the framework. Thanks to this, the developer does not need
	 * to register each service individually providing a full stack framework
	 */
	
	class FactoryDefault extends \ManaPHP\Di implements \ManaPHP\DiInterface {

		/**
		 * \ManaPHP\DI\FactoryDefault constructor
		 */
		public function __construct(){ }

	}
}
