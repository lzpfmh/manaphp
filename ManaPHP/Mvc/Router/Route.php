<?php 

namespace ManaPHP\Mvc\Router {


	/**
	 * ManaPHP\Mvc\Router\Route
	 *
	 * This class represents every route added to the router
	 *
	 * NOTE_PHP:
	 * 	Hostname Constraints has been removed by PHP implementation
	 */
	
	class Route implements RouteInterface {

		/**
		 * @var string
		 */
		protected $_pattern;

		/**
		 * @var string
		 */
		protected $_compiledPattern;

		/**
		 * @var array
		 */
		protected $_paths;

		/**
		 * @var array|null|string
		 */
		protected $_methods;


		/**
		 * @var callable
		 */
		protected $_beforeMatch;

		/**
		 * @var \ManaPHP\Mvc\Router\GroupInterface
		 */
		protected $_group;

		/**
		 * \ManaPHP\Mvc\Router\Route constructor
		 *
		 * @param string $pattern
		 * @param array $paths
		 * @param array|string $httpMethods
		 * @throws \ManaPHP\Mvc\Router\Exception
		 */
		public function __construct($pattern, $paths=null, $httpMethods=null){
			$this->_pattern=$pattern;
			$this->_compiledPattern =$this->compilePattern($pattern);
			$this->_paths =self::getRoutePaths($paths);

			$this->_methods =$httpMethods;
		}


		/**
		 * Replaces placeholders from pattern returning a valid PCRE regular expression
		 *
		 * @param string $pattern
		 * @return string
		 */
		public function compilePattern($pattern){
			// If a pattern contains ':', maybe there are placeholders to replace
			if(strpos($pattern,':') !==false){
				$pattern =str_replace('/:module','/{module:[\w-]+}',$pattern);
				$pattern=str_replace('/:controller','/{controller:[\w-]+}',$pattern);
				$pattern =str_replace('/:namespace','/{namespace:[\w-]+}',$pattern);
				$pattern =str_replace('/:action','/{action:[\w-]+}',$pattern);
				$pattern=str_replace('/:params','/{params:.+}',$pattern);
				$pattern=str_replace('/:int','/(\d+)',$pattern);
			}

			if(strpos($pattern,'{') !==false){
				$pattern=$this->extractNamedParams($pattern);
			}

			if(strpos($pattern,'(') !==false ||strpos($pattern,'[') !==false){
				return '#^' . $pattern . '$#';
			}else{
				return $pattern;
			}
		}

		/**
		 * Extracts parameters from a string
		 * @param string $pattern
		 * @return string
		 */
		public function extractNamedParams($pattern){
			if(strpos($pattern,'{') ===false){
				return $pattern;
			}

			$left_token='@_@';
			$right_token='!_!';
			$need_restore_token =false;

			if(preg_match('#{\d#',$pattern) ===1){
				if(strpos($pattern,$left_token) ===false &&strpos($pattern,$right_token) ===false){
					$need_restore_token =true;
					$pattern =preg_replace('#{(\d+,?\d*)}#',$left_token.'\1'.$right_token,$pattern);
				}
			}

			if(preg_match_all('#{([A-Z].*)}#Ui',$pattern,$matches,PREG_SET_ORDER) >0){
				foreach($matches as $match){

					if(strpos($match[0],':') ===false){
						$pattern=str_replace($match[0],'(?<'.$match[1].'>[\w_]+)',$pattern);
					}else{
						$parts =explode(':',$match[1]);
						$pattern =str_replace($match[0],'(?<'.$parts[0].'>'.$parts[1].')',$pattern);
					}
				}
			}

			if($need_restore_token){
				$pattern=str_replace([$left_token,$right_token],['{','}'],$pattern);
			}
			return $pattern;
		}

		/**
		 * Returns routePaths
		 * @param string|array $paths
		 * @return array
		 * @throws \ManaPHP\Mvc\Router\Exception
		 */
		public static function getRoutePaths($paths=null){
			if($paths !==null){
				if(is_string($paths)){
					$parts =explode('::',$paths);
					if(count($parts) ===3){
						$moduleName =$parts[0];
						$controllerName=$parts[1];
						$actionName=$parts[2];
					}elseif(count($parts)===2){
						$controllerName=$parts[0];
						$actionName=$parts[1];
					}else{
						$controllerName=$parts[0];
					}

					$routePaths =[];
					if(isset($moduleName)){
						$routePaths['module']=$moduleName;
					}

					if(isset($controllerName)){
						if(strpos($controllerName,'\\') !==false){
							throw new Exception('-- invalid part: '.$controllerName);
						}else{
							// Always pass the controller to lowercase
							$routePaths['controller']=self::_uncamelize($controllerName);
						}
					}

					if(isset($actionName)){
						$routePaths['action']=$actionName;
					}
				}else if(is_array($paths)){
					$routePaths =$paths;
				}else{
					throw new Exception('--paths must be a string or array.');
				}
			}else{
				$routePaths=[];
			}

			return $routePaths;
		}

		static protected function _uncamelize($str){
			$first=true;
			$str =preg_replace_callback('/([A-Z])/',function($matches) use(&$first){
				if($first){
					$first =false;
					return strtolower($matches[1]);
				}else{
					return '_'.strtolower($matches[1]);
				}
			},$str);
			return $str;
		}
		/**
		 * Sets a callback that is called if the route is matched.
		 * The developer can implement any arbitrary conditions here
		 * If the callback returns false the route is treaded as not matched
		 *
		 * @param callback $callback
		 * @return static
		 * @throws \ManaPHP\Mvc\Router\Exception
		 */
		public function beforeMatch($callback){
			if(!is_callable($callback)) {
				throw new Exception('Before-Match callback is not callable');
			}
			$this->_beforeMatch =$callback;
			return $this;
		}


		/**
		 * Returns the 'before match' callback if any
		 *
		 * @return mixed
		 */
		public function getBeforeMatch(){
			return $this->_beforeMatch;
		}


		/**
		 * Returns the route's pattern
		 *
		 * @return string
		 */
		public function getPattern(){
			return $this->_pattern;
		}


		/**
		 * Returns the route's compiled pattern
		 *
		 * @return string
		 */
		public function getCompiledPattern(){
			return $this->_compiledPattern;
		}


		/**
		 * Returns the paths
		 *
		 * @return array
		 */
		public function getPaths(){
			return $this->_paths;
		}


		/**
		 * Returns the paths using positions as keys and names as values
		 *
		 * @return array
		 */
		public function getReversedPaths(){
			$reversed=[];
			foreach($this->_paths as $path=>$position){
				$reversed[$position]=$path;
			}

			return $reversed;
		}


		/**
		 * Sets a set of HTTP methods that constraint the matching of the route (alias of via)
		 *
		 *<code>
		 * $route->setHttpMethods('GET');
		 * $route->setHttpMethods(array('GET', 'POST'));
		 *</code>
		 *
		 * @param string|array $httpMethods
		 * @return static
		 */
		public function setHttpMethods($httpMethods){
			$this->_methods =$httpMethods;
			return $this;
		}


		/**
		 * Returns the HTTP methods that constraint matching the route
		 *
		 * @return string|array
		 */
		public function getHttpMethods(){
			return $this->_methods;
		}

		/**
		 * Sets the group associated with the route
		 *
		 * @param \ManaPHP\Mvc\Router\Group $group
		 * @return static
		 */
		public function setGroup($group){
			$this->_group=$group;
			return $this;
		}


		/**
		 * Returns the group associated with the route
		 *
		 * @return \ManaPHP\Mvc\Router\Group|null
		 */
		public function getGroup(){
			return $this->_group;
		}

		/**
		 * @param string $handle_uri
		 * @param array|null $matches
		 * @return bool
		 * @throws \ManaPHP\Mvc\Router\Exception
		 */
		public function isMatched($handle_uri, &$matches){
			$matches =null;

			$methods =$this->getHttpMethods();
			if($methods !==null){
				if(is_string($methods)){
					if($methods !==$_SERVER['REQUEST_METHOD']){
						return false;
					}
				}else{
					if(!in_array($_SERVER['REQUEST_METHOD'],$methods,true)){
						return false;
					}
				}
			}

			$pattern =$this->getCompiledPattern();

			if(strpos($pattern,'^') !==false){
				$r=preg_match($pattern,$handle_uri,$matches);
				if($r ===false){
					throw new Exception('--invalid PCRE: '.$pattern. ' for '. $this->getPattern());
				}

				$is_matched =$r===1;
			}else{
				$is_matched =$pattern===$handle_uri;
			}

			if($is_matched){
				$beforeMatch=$this->getBeforeMatch();
				if($beforeMatch !==null){
					if(!is_callable($beforeMatch)) {
						throw new Exception('Before-Match callback is not callable in matched route');
					}

					$is_matched=call_user_func_array($this->getBeforeMatch(),[$handle_uri, $this]);
				}
			}

			return $is_matched;
		}
	}
}
