<?php 

namespace ManaPHP\Mvc\Router {

	/**
	 * ManaPHP\Mvc\Router\Group
	 *
	 * Helper class to create a group of routes with common attributes
	 *
	 * NOTE_PHP:
	 * 		1.Hostname Constraints has been removed by PHP implementation
	 * 		2. remove clear method
	 *
	 *<code>
	 * $router = new ManaPHP\Mvc\Router();
	 *
	 * //Create a group with a common module and controller
	 * $blog = new ManaPHP\Mvc\Router\Group(array(
	 * 	'module' => 'blog',
	 * 	'controller' => 'index'
	 * ));
	 *
	 * //All the routes start with /blog
	 * $blog->setPrefix('/blog');
	 *
	 * //Add a route to the group
	 * $blog->add('/save', array(
	 * 	'action' => 'save'
	 * ));
	 *
	 * //Add another route to the group
	 * $blog->add('/edit/{id}', array(
	 * 	'action' => 'edit'
	 * ));
	 *
	 * //This route maps to a controller different than the default
	 * $blog->add('/blog', array(
	 * 	'controller' => 'about',
	 * 	'action' => 'index'
	 * ));
	 *
	 * //Add the group to the router
	 * $router->mount($blog);
	 *</code>
	 *
	 */
	
	class Group {

		/*
		 * var string
		 */
		protected $_prefix;

		/**
		 * @var array
		 */
		protected $_paths;

		/**
		 * @var \ManaPHP\Mvc\Router\RouteInterface[]
		 */
		protected $_routes;

		/**
		 * @var string
		 */
		protected $_beforeMatch;


		/**
		 * \ManaPHP\Mvc\Router\Group constructor
		 *
		 * @param array $paths
		 */
		public function __construct($paths=null){
			if(is_array($paths)){
				$this->_paths =$paths;
			}
		}


		/**
		 * Set a common uri prefix for all the routes in this group
		 *
		 * @param string $prefix
		 * @return \ManaPHP\Mvc\Router\GroupInterface
		 */
		public function setPrefix($prefix){
			$this->_prefix=$prefix;
			return $this;
		}


		/**
		 * Returns the common prefix for all the routes
		 *
		 * @return string
		 */
		public function getPrefix(){
			return $this->_prefix;
		}


		/**
		 * Set a before-match condition for the whole group,
		 * The developer can implement any arbitrary conditions here
		 * If the callback returns false the route is treated as not matched
		 *
		 * @param callable $beforeMatch
		 * @return \ManaPHP\Mvc\Router\GroupInterface
		 * @throws
		 */
		public function beforeMatch($beforeMatch){
			if(!is_callable($beforeMatch)) {
				throw new Exception("Before-Match callback is not callable");
			}
			$this->_beforeMatch =$beforeMatch;
			return $this;
		}


		/**
		 * Returns the before-match condition if any
		 *
		 * @return callable
		 */
		public function getBeforeMatch(){
			return $this->_beforeMatch;
		}


		/**
		 * Set common paths for all the routes in the group
		 *
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\GroupInterface
		 */
		public function setPaths($paths){
			$this->_paths =$paths;
			return $this;
		}


		/**
		 * Returns the common paths defined for this group
		 *
		 * @return array
		 */
		public function getPaths(){
			return $this->_paths;
		}


		/**
		 * Returns the routes added to the group
		 *
		 * @return \ManaPHP\Mvc\Router\RouteInterface[]
		 */
		public function getRoutes(){
			return $this->_routes;
		}


		/**
		 * Adds a route applying the common attributes
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @param array $httpMethods
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		protected function _addRoute($pattern, $paths=null, $httpMethods=null){
			$route =new Route($this->_prefix.$pattern,is_array($paths)?array_merge($this->_paths,$paths):$this->_paths, $httpMethods);
			$route->setGroup($this);
			$this->_routes[]=$route;

			return $route;
		}


		/**
		 * Adds a route to the router on any HTTP method
		 *
		 *<code>
		 * $router->add('/about', 'About::index');
		 *</code>
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @param string $httpMethods
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function add($pattern, $paths=null, $httpMethods=null){
			return $this->_addRoute($pattern,$paths,$httpMethods);
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is GET
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addGet($pattern, $paths=null){
			return $this->_addRoute($pattern, $paths, "GET");
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is POST
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addPost($pattern, $paths=null){
			return $this->_addRoute($pattern, $paths, "POST");
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is PUT
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addPut($pattern, $paths=null){
			return $this->_addRoute($pattern, $paths, "PUT");
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is PATCH
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addPatch($pattern, $paths=null){
			return $this->_addRoute($pattern, $paths, "PATCH");
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is DELETE
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addDelete($pattern, $paths=null){
			return $this->_addRoute($pattern, $paths, "DELETE");
		}


		/**
		 * Add a route to the router that only match if the HTTP method is OPTIONS
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addOptions($pattern, $paths=null){
			return $this->_addRoute($pattern, $paths, "OPTIONS");
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is HEAD
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addHead($pattern, $paths=null){
			return $this->_addRoute($pattern, $paths, "HEAD");
		}
	}
}
