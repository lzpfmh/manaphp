<?php 

namespace ManaPHP\Mvc {

	use ManaPHP\Mvc\Router\Exception;
	use ManaPHP\Mvc\Router\Route;
	use ManaPHP\Di\InjectionAwareInterface;
	use ManaPHP\Events\EventsAwareInterface;

	/**
	 * ManaPHP\Mvc\Router
	 *
	 * <p>ManaPHP\Mvc\Router is the standard framework router. Routing is the
	 * process of taking a URI endpoint (that part of the URI which comes after the base URL) and
	 * decomposing it into parameters to determine which module, controller, and
	 * action of that controller should receive the request</p>
	 *
	 *<code>
	 *
	 *	$router = new ManaPHP\Mvc\Router();
	 *
	 *  $router->add(
	 *		"/documentation/{chapter}/{name}.{type:[a-z]+}",
	 *		array(
	 *			"controller" => "documentation",
	 *			"action"     => "show"
	 *		)
	 *	);
	 *
	 *	$router->handle();
	 *
	 *	echo $router->getControllerName();
	 *</code>
	 *
	 */
	
	class Router implements RouterInterface, InjectionAwareInterface, EventsAwareInterface {

		const URI_SOURCE_GET_URL = 0;

		const URI_SOURCE_SERVER_REQUEST_URI = 1;

		/**
		 * @var \ManaPHP\DiInterface
		 */
		protected $_dependencyInjector=null;

		/**
		 * @var \ManaPHP\Events\ManagerInterface
		 */
		protected $_eventsManager=null;

		/**
		 * @var int
		 */
		protected $_uriSource =self::URI_SOURCE_GET_URL;

		/**
		 * @var string
		 */
		protected $_namespace=null;

		/**
		 * @var string
		 */
		protected $_module=null;

		/**
		 * @var string
		 */
		protected $_controller=null;

		/**
		 * @var string
		 */
		protected $_action=null;

		/**
		 * @var array
		 */
		protected $_params=[];

		/**
		 * @var \ManaPHP\Mvc\Router\RouteInterface[]
		 */
		protected $_routes;

		/**
		 * @var \ManaPHP\Mvc\Router\RouteInterface
		 */
		protected $_matchedRoute=null;

		/**
		 * @var boolean
		 */
		protected $_wasMatched=false;

		/**
		 * @var string
		 */
		protected $_defaultNamespace=null;

		/**
		 * @var string
		 */
		protected $_defaultModule=null;

		/**
		 * @var string
		 */
		protected $_defaultController=null;

		/**
		 * @var string
		 */
		protected $_defaultAction=null;

		/**
		 * @var array
		 */
		protected $_defaultParams=[];

		/**
		 * @var boolean
		 */
		protected $_removeExtraSlashes=false;

		/**
		 * @var array
		 */
		protected $_notFoundPaths=null;

		/**
		 * ManaPHP\Mvc\Router constructor
		 *
		 * @param boolean $defaultRoutes
		 */
		public function __construct($defaultRoutes=true){
			$this->_routes=[];

			if($defaultRoutes){
				$this->_routes[]=new Route('/');
				$this->_routes[]=new Route('/:controller');
				$this->_routes[]=new Route('/:controller/:action');
				$this->_routes[]=new Route('/:controller/:action:/:params');
			}
		}


		/**
		 * Sets the dependency injector
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector){
			$this->_dependencyInjector =$dependencyInjector;
		}


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){
			return $this->_dependencyInjector;
		}

		/**
		 * Sets the events manager
		 * @param \ManaPHP\Events\EventsAwareInterface $eventsManager
		 */
		public function setEventsManager($eventsManager){
			$this->_eventsManager =$eventsManager;
		}

		/**
		 * Returns the internal event manager
		 * @return \ManaPHP\Events\EventsAwareInterface
		 */
		public function getEventsManager()
		{
			return $this->_eventsManager;
		}

		/**
		 * Get rewrite info. This info is read from $_GET['_url']. This returns '/' if the rewrite information cannot be read
		 *
		 * @return string
		 * @throws
		 */
		public function getRewriteUri(){
			if($this->_uriSource ===self::URI_SOURCE_GET_URL){
				if(!isset($_GET['_url'])){
					if($_SERVER['SCRIPT_NAME'] ==='/index.php'){
						$real_url='/';
					}else{
						throw new Exception('--$_GET["_url"] not set, may be .htaccess has incorrect config.');
					}

				}else{
					$real_url =$_GET['_url'];
				}
			}elseif($this->_uriSource ===self::URI_SOURCE_SERVER_REQUEST_URI){
				if(!isset($_SERVER['REQUEST_URI'])){
					throw new Exception('--$_SERVER["REQUEST_URI"] not set.');
				}else{
					$real_url =explode('?',$_SERVER['REQUEST_URI'],2)[0];
				}
			}else{
				throw new Exception('--invalid URI_SOURCE');
			}

			return $real_url===''?'/':$real_url;
		}


		/**
		 * Sets the URI source. One of the URI_SOURCE_* constants
		 *
		 *<code>
		 *	$router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
		 *</code>
		 *
		 * @param int $uriSource
		 * @return \ManaPHP\Mvc\RouterInterface
		 */
		public function setUriSource($uriSource){
			$this->_uriSource =$uriSource;
			return $this;
		}


		/**
		 * Set whether router must remove the extra slashes in the handled routes
		 *
		 * @param boolean $remove
		 * @return \ManaPHP\Mvc\RouterInterface
		 */
		public function removeExtraSlashes($remove){
			$this->_removeExtraSlashes=$remove;
			return $this;
		}


		/**
		 * Sets the name of the default namespace
		 *
		 * @param string $namespaceName
		 * @return \ManaPHP\Mvc\RouterInterface
		 */
		public function setDefaultNamespace($namespaceName){
			$this->_defaultNamespace =$namespaceName;
			return $this;
		}


		/**
		 * Returns the name of the default namespace
		 *
		 * @return string
		 */
		public function getDefaultNamespace(){
			return $this->_defaultNamespace;
		}


		/**
		 * Sets the name of the default module
		 *
		 * @param string $moduleName
		 * @return \ManaPHP\Mvc\RouterInterface
		 */
		public function setDefaultModule($moduleName){
			$this->_defaultModule =$moduleName;
			return $this;
		}


		/**
		 * Returns the name of the default module
		 *
		 * @return string
		 */
		public function getDefaultModule(){
			return $this->_defaultModule;
		}


		/**
		 * Sets the default controller name
		 *
		 * @param string $controllerName
		 * @return \ManaPHP\Mvc\RouterInterface
		 */
		public function setDefaultController($controllerName){
			$this->_defaultController =$controllerName;
			return $this;
		}


		/**
		 * Returns the default controller name
		 *
		 * @return string
		 */
		public function getDefaultController(){
			return $this->_defaultController;
		}


		/**
		 * Sets the default action name
		 *
		 * @param string $actionName
		 * @return \ManaPHP\Mvc\RouterInterface
		 */
		public function setDefaultAction($actionName){
			$this->_defaultAction =$actionName;
			return $this;
		}


		/**
		 * Returns the default action name
		 *
		 * @return string
		 */
		public function getDefaultAction(){
			return $this->_defaultAction;
		}


		/**
		 * Handles routing information received from the rewrite engine
		 *
		 *<code>
		 * //Read the info from the rewrite engine
		 * $router->handle();
		 *
		 * //Manually passing an URL
		 * $router->handle('/posts/edit/1');
		 *</code>
		 *
		 * @param string $uri
		 * @return boolean
		 * @throws
		 */
		public function handle($uri=null){
			/**
			 *  @var $request \ManaPHP\Http\RequestInterface
			 */

			$uri=($uri===null||$uri==='')?$this->getRewriteUri():$uri;

			if($this->_removeExtraSlashes && $uri !=='/'){
				$handle_uri =rtrim($uri,'/');
			}else{
				$handle_uri =$uri;
			}

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('router:beforeCheckRoutes',$this);
			}

			$route_found=false;
			$parts=[];

			/**
			 * Routes are traversed in reversed order
			 */
			for($i =count($this->_routes)-1; $i>=0; $i--){
				$route =$this->_routes[$i];

				$route_found=$route->isMatched($handle_uri,$matches);

				if($route_found){
					$paths =$route->getPaths();
					$parts=$paths;

					if(is_array($matches)){
						foreach($matches as $k=>$v){
							if(is_string($k)){
								$paths[$k]=$v;
							}
						}
						$parts =$paths;

						foreach($paths as $part=>$position){
							if(is_integer($position) &&isset($matches[$position])){
								$parts[$part]=$matches[$position];
							}
						}
					}

					$this->_matchedRoute =$route;
					break;
				}
			}

			/**
			 * Update the wasMatched property indicating if the route was matched
			 */
			$this->_wasMatched =$route_found;

			/**
			 * The route was n't found, try to use the not-found paths
			 */
			if(!$route_found){
				if($this->_notFoundPaths !==null){
					$parts =Route::getRoutePaths($this->_notFoundPaths);
					$route_found =true;
				}
			}

			$this->_namespace =$this->_defaultNamespace;
			$this->_module =$this->_defaultModule;
			$this->_controller =$this->_defaultController;
			$this->_action =$this->_defaultAction;
			$this->_params =$this->_defaultParams;

			if($route_found){
				if(isset($parts['namespace'])){
					if(!is_numeric($parts['namespace'])){
						$this->_namespace=$parts['namespace'];
					}
					unset($parts['namespace']);
				}

				if(isset($parts['module'])){
					if(!is_numeric($parts['module'])){
						$this->_module =$parts['module'];
					}
					unset($parts['module']);
				}

				if(isset($parts['controller'])){
					if(!is_numeric($parts['controller'])){
						$this->_controller=$parts['controller'];
					}
					unset($parts['controller']);
				}

				if(isset($parts['action'])){
					if(!is_numeric($parts['action'])){
						$this->_action =$parts['action'];
					}
					unset($parts['action']);
				}

				$params=[];
				if(isset($parts['params'])){
					if(is_string($parts['params'])){
						$params_str=trim($parts['params'],'/');
						if($params_str !==''){
							$params =explode('/',$params_str);
						}
					}

					unset($parts['params']);
				}

				$this->_params=array_merge($params,$parts);
			}

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('router:afterCheckRoutes',$this);
			}

			return $route_found;
		}


		/**
		 * Adds a route to the router without any HTTP constraint
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
		public function add($pattern, $paths, $httpMethods=null){
			$route =new Route($pattern,$paths,$httpMethods);
			$this->_routes[]=$route;

			return $route;
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is GET
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addGet($pattern, $paths){
			return $this->add($pattern,$paths,'GET');
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is POST
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addPost($pattern, $paths){
			return $this->add($pattern,$paths,'POST');
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is PUT
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addPut($pattern, $paths){
			return $this->add($pattern,$paths,'PUT');
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is PATCH
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addPatch($pattern, $paths){
			return $this->add($pattern,$paths,'PATCH');
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is DELETE
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addDelete($pattern, $paths){
			return $this->add($pattern,$paths,'DELETE');
		}


		/**
		 * Add a route to the router that only match if the HTTP method is OPTIONS
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addOptions($pattern, $paths){
			return $this->add($pattern,$paths,'OPTIONS');
		}


		/**
		 * Adds a route to the router that only match if the HTTP method is HEAD
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function addHead($pattern, $paths){
			return $this->add($pattern,$paths,'HEAD');
		}

		/**
		 * Mounts a group of routes in the router
		 *
		 * @param \ManaPHP\Mvc\Router\GroupInterface $group
		 * @return \ManaPHP\Mvc\RouterInterface
		 */
		public function mount($group){
			$groupRoutes=$group->getRoutes();

			$beforeMatch =$group->getBeforeMatch();
			if($beforeMatch !==null){
				foreach($groupRoutes as $route){
					$route->beforeMatch($beforeMatch);
				}
			}

			$this->_routes =array_merge($this->_routes,$groupRoutes);

			return $this;
		}


		/**
		 * Set a group of paths to be returned when none of the defined routes are matched
		 *
		 * @param array $paths
		 * @return \ManaPHP\Mvc\RouterInterface
		 */
		public function notFound($paths){
			$this->_notFoundPaths =$paths;
			return $this;
		}

		/**
		 * Returns the processed namespace name
		 *
		 * @return string
		 */
		public function getNamespaceName(){
			return $this->_namespace;
		}


		/**
		 * Returns the processed module name
		 *
		 * @return string
		 */
		public function getModuleName(){
			return $this->_module;
		}


		/**
		 * Returns the processed controller name
		 *
		 * @return string
		 */
		public function getControllerName(){
			return $this->_controller;
		}


		/**
		 * Returns the processed action name
		 *
		 * @return string
		 */
		public function getActionName(){
			return $this->_action;
		}


		/**
		 * Returns the processed parameters
		 *
		 * @return array
		 */
		public function getParams(){
			return $this->_params;
		}


		/**
		 * Returns the route that matches the handled URI
		 *
		 * @return \ManaPHP\Mvc\Router\RouteInterface
		 */
		public function getMatchedRoute(){
			return $this->_matchedRoute;
		}

		/**
		 * Checks if the router matches any of the defined routes
		 *
		 * @return bool
		 */
		public function wasMatched(){
			return $this->_wasMatched;
		}


		/**
		 * Returns all the routes defined in the router
		 *
		 * @return \ManaPHP\Mvc\Router\RouteInterface[]
		 */
		public function getRoutes(){
			return $this->_routes;
		}
	}
}
