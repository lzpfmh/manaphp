<?php 

namespace ManaPHP\Mvc {

	/**
	 * ManaPHP\Mvc\Application
	 *
	 * This component encapsulates all the complex operations behind instantiating every component
	 * needed and integrating it with the rest to allow the MVC pattern to operate as desired.
	 *
	 *<code>
	 *
	 * class Application extends \ManaPHP\Mvc\Application
	 * {
	 *
	 *		/\**
	 *		 * Register the services here to make them general or register
	 *		 * in the ModuleDefinition to make them module-specific
	 *		 *\/
	 *		protected function _registerServices()
	 *		{
	 *
	 *		}
	 *
	 *		/\**
	 *		 * This method registers all the modules in the application
	 *		 *\/
	 *		public function main()
	 *		{
	 *			$this->registerModules(array(
	 *				'frontend' => array(
	 *					'className' => 'Multiple\Frontend\Module',
	 *					'path' => '../apps/frontend/Module.php'
	 *				),
	 *				'backend' => array(
	 *					'className' => 'Multiple\Backend\Module',
	 *					'path' => '../apps/backend/Module.php'
	 *				)
	 *			));
	 *		}
	 *	}
	 *
	 *	$application = new Application();
	 *	$application->main();
	 *
	 *</code>
	 */
	
	class Application extends \ManaPHP\DI\Injectable implements \ManaPHP\Events\EventsAwareInterface, \ManaPHP\DI\InjectionAwareInterface {

		protected $_defaultModule;

		protected $_modules;

		protected $_moduleObject;

		protected $_implicitView;

		/**
		 * \ManaPHP\Mvc\Application
		 *
		 * @param \ManaPHP\Di $dependencyInjector
		 */
		public function __construct($dependencyInjector=null){ }


		/**
		 * By default. The view is implicitly buffering all the output
		 * You can full disable the view component using this method
		 *
		 * @param boolean $implicitView
		 * @return \ManaPHP\Mvc\Application
		 */
		public function useImplicitView($implicitView){ }


		/**
		 * Register an array of modules present in the application
		 *
		 *<code>
		 *	$this->registerModules(array(
		 *		'frontend' => array(
		 *			'className' => 'Multiple\Frontend\Module',
		 *			'path' => '../apps/frontend/Module.php'
		 *		),
		 *		'backend' => array(
		 *			'className' => 'Multiple\Backend\Module',
		 *			'path' => '../apps/backend/Module.php'
		 *		)
		 *	));
		 *</code>
		 *
		 * @param array $modules
		 * @param boolean $merge
		 * @param \ManaPHP\Mvc\Application
		 */
		public function registerModules($modules, $merge=null){ }


		/**
		 * Return the modules registered in the application
		 *
		 * @return array
		 */
		public function getModules(){ }


		/**
		 * Sets the module name to be used if the router doesn't return a valid module
		 *
		 * @param string $defaultModule
		 * @return \ManaPHP\Mvc\Application
		 */
		public function setDefaultModule($defaultModule){ }


		/**
		 * Returns the default module name
		 *
		 * @return string
		 */
		public function getDefaultModule(){ }


		/**
		 * Handles a MVC request
		 *
		 * @param string $uri
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function handle($uri=null){ }

	}
}
