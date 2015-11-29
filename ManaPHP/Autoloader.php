<?php 

namespace ManaPHP {

	/**
	 * ManaPHP\Loader
	 *
	 * This component helps to load your project classes automatically based on some conventions
	 *
	 *<code>
	 * //Creates the autoloader
	 * $loader = new ManaPHP\Loader();
	 *
	 * //Register some namespaces
	 * $loader->registerNamespaces(array(
	 *   'Example\Base' => 'vendor/example/base/',
	 *   'Example\Adapter' => 'vendor/example/adapter/',
	 *   'Example' => 'vendor/example/'
	 * ));
	 *
	 * //register autoloader
	 * $loader->register();
	 *
	 * //Requiring this class will automatically include file vendor/example/adapter/Some.php
	 * $adapter = Example\Adapter\Some();
	 *</code>
	 */

	class Autoloader implements \ManaPHP\Events\EventsAwareInterface {

		protected $_eventsManager;

		protected $_foundPath;

		protected $_checkedPath;

		protected $_prefixes;

		protected $_classes;

		protected $_extensions;

		protected $_namespaces;

		protected $_directories;

		protected $_registered;

		/**
		 * \ManaPHP\Autoloader constructor
		 */
		public function __construct(){ }


		/**
		 * Sets the events manager
		 *
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 */
		public function setEventsManager($eventsManager){ }


		/**
		 * Returns the internal event manager
		 *
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		public function getEventsManager(){ }


		/**
		 * Sets an array of extensions that the Autoloader must try in each attempt to locate the file
		 *
		 * @param array $extensions
		 * @param boolean $merge
		 * @return \ManaPHP\Autoloader
		 */
		public function setExtensions($extensions){ }


		/**
		 * Return file extensions registered in the Autoloader
		 *
		 * @return boolean
		 */
		public function getExtensions(){ }


		/**
		 * Register namespaces and their related directories
		 *
		 * @param array $namespaces
		 * @param boolean $merge
		 * @return \ManaPHP\Autoloader
		 */
		public function registerNamespaces($namespaces, $merge=null){ }


		/**
		 * Return current namespaces registered in the autoloader
		 *
		 * @return array
		 */
		public function getNamespaces(){ }


		/**
		 * Register directories on which "not found" classes could be found
		 *
		 * @param array $prefixes
		 * @param boolean $merge
		 * @return \ManaPHP\Autoloader
		 */
		public function registerPrefixes($prefixes, $merge=null){ }


		/**
		 * Return current prefixes registered in the autoloader
		 *
		 * @param array
		 */
		public function getPrefixes(){ }


		/**
		 * Register directories on which "not found" classes could be found
		 *
		 * @param array $directories
		 * @param boolean $merge
		 * @return \ManaPHP\Autoloader
		 */
		public function registerDirs($directories, $merge=null){ }


		/**
		 * Return current directories registered in the autoloader
		 *
		 * @param array
		 */
		public function getDirs(){ }


		/**
		 * Register classes and their locations
		 *
		 * @param array $classes
		 * @param boolean $merge
		 * @return \ManaPHP\Autoloader
		 */
		public function registerClasses($classes, $merge=null){ }


		/**
		 * Return the current class-map registered in the autoloader
		 *
		 * @param array
		 */
		public function getClasses(){ }


		/**
		 * Register the autoload method
		 *
		 * @return \ManaPHP\Autoloader
		 */
		public function register(){ }


		/**
		 * Unregister the autoload method
		 *
		 * @return \ManaPHP\Autoloader
		 */
		public function unregister(){ }


		/**
		 * Makes the work of autoload registered classes
		 *
		 * @param string $className
		 * @return boolean
		 */
		public function autoLoad($className){ }


		/**
		 * Get the path when a class was found
		 *
		 * @return string
		 */
		public function getFoundPath(){ }


		/**
		 * Get the path the Autoloader is checking for a path
		 *
		 * @return string
		 */
		public function getCheckedPath(){ }

	}
}
