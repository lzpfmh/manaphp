<?php 

namespace ManaPHP\Mvc\Router {

	/**
	 * ManaPHP\Mvc\Router\RouteInterface initializer
	 */
	
	interface RouteInterface {

		/**
		 * Sets a callback that is called if the route is matched.
		 * The developer can implement any arbitrary conditions here
		 * If the callback returns false the route is treated as not matched
		 *
		 * @param callable callback
		 * @return static
		 */
		public function beforeMatch($callback);

		/**
		 * Sets a set of HTTP methods that constraint the matching of the route
		 *
		 * @param string|array $httpMethods
		 */
		public function setHttpMethods($httpMethods);


		/**
		 * Returns the paths
		 *
		 * @return array
		 */
		public function getPaths();

		/**
		 * @param string $handle_uri
		 * @param array $matches
		 * @return bool
		 */
		public function isMatched($handle_uri, &$matches);
	}
}
