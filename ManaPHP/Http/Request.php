<?php 

namespace ManaPHP\Http {

	use \ManaPHP\Http\Request\Exception;
	use \ManaPHP\Di\InjectionAwareInterface;

	/**
	 * ManaPHP\Http\Request
	 *
	 * <p>Encapsulates request information for easy and secure access from application controllers.</p>
	 *
	 * <p>The request object is a simple value object that is passed between the dispatcher and controller classes.
	 * It packages the HTTP request environment.</p>
	 *
	 *<code>
	 *	$request = new ManaPHP\Http\Request();
	 *	if ($request->isPost() == true) {
	 *		if ($request->isAjax() == true) {
	 *			echo 'Request was made using POST and AJAX';
	 *		}
	 *	}
	 *</code>
	 *
	 */
	
	class Request implements RequestInterface,InjectionAwareInterface {

		/**
		 * @var \ManaPHP\DiInterface
		 */
		protected $_dependencyInjector;

		/**
		 * @var \ManaPHP\FilterInterface
		 */
		protected $_filter;

		protected $_rawBody;

		/**
		 * @var array
		 */
		protected $_putCache=null;

		/**
		 * @var \ManaPHP\Http\Request\FileInterface[]
		 */
		protected $_files;

		function __construct(){
			if($this->isPut()){
				parse_str($this->getRawBody(),$this->_putCache);
			}
		}
		/**
		 * Sets the dependency injector
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @return \ManaPHP\Http\RequestInterface
		 */
		public function setDI($dependencyInjector){
			$this->_dependencyInjector =$dependencyInjector;
			return $this;
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
		 * Helper to get data from superglobals, applying filters if needed.
		 * If no parameters are given the superglobal is returned.
		 *
		 * @param array $source
		 * @param string $name
		 * @param mixed $filters
		 * @param mixed $defaultValue
		 * @param boolean $notAllowEmpty
		 * @return string
		 * @throws
		 */
		protected function _getHelper($source, $name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false){
			if($filters !==null){
				throw new Exception('filter not supported');
			}

			if($name ===null){
				return $source;
			}

			if(!isset($source[$name])){
				return $defaultValue;
			}

			if(empty($source[$name]) &&$notAllowEmpty ===true){
				return $defaultValue;
			}

			return $source[$name];
		}

		/**
		 * Gets a variable from the $_REQUEST superglobal applying filters if needed.
		 * If no parameters are given the $_REQUEST superglobal is returned
		 *
		 *<code>
		 *	//Returns value from $_REQUEST["user_email"] without sanitizing
		 *	$userEmail = $request->get("user_email");
		 *
		 *	//Returns value from $_REQUEST["user_email"] with sanitizing
		 *	$userEmail = $request->get("user_email", "email");
		 *</code>
		 *
		 * @param string $name
		 * @param string|array $filters
		 * @param mixed $defaultValue
		 * @param boolean $notAllowEmpty
		 * @return mixed
		 */
		public function get($name=null, $filters=null, $defaultValue=null,$notAllowEmpty=false){
			return $this->_getHelper($_REQUEST,$name,$filters,$defaultValue,$notAllowEmpty);
		}

		/**
		 * Gets variable from $_GET superglobal applying filters if needed
		 * If no parameters are given the $_GET superglobal is returned
		 *
		 *<code>
		 *	//Returns value from $_GET["id"] without sanitizing
		 *	$id = $request->getGet("id");
		 *
		 *	//Returns value from $_GET["id"] with sanitizing
		 *	$id = $request->getGet("id", "int");
		 *
		 *	//Returns value from $_GET["id"] with a default value
		 *	$id = $request->getGet("id", null, 150);
		 *</code>
		 *
		 * @param string $name
		 * @param string|array $filters
		 * @param mixed $defaultValue
		 * @param boolean $notAllowEmpty
		 * @return mixed
		 */
		public function getGet($name=null, $filters=null, $defaultValue=null,$notAllowEmpty=false){
			return $this->_getHelper($_GET,$name,$filters,$defaultValue,$notAllowEmpty);
		}


		/**
		 * Gets a variable from the $_POST superglobal applying filters if needed
		 * If no parameters are given the $_POST superglobal is returned
		 *
		 *<code>
		 *	//Returns value from $_POST["user_email"] without sanitizing
		 *	$userEmail = $request->getPost("user_email");
		 *
		 *	//Returns value from $_POST["user_email"] with sanitizing
		 *	$userEmail = $request->getPost("user_email", "email");
		 *</code>
		 *
		 * @param string $name
		 * @param string|array $filters
		 * @param mixed $defaultValue
		 * @param boolean $notAllowEmpty
		 * @return mixed
		 */
		public function getPost($name=null, $filters=null, $defaultValue=null,$notAllowEmpty=false){
			return $this->_getHelper($_POST,$name,$filters,$defaultValue,$notAllowEmpty);
		}


		/**
		 * Gets a variable from put request
		 *
		 *<code>
		 *	$userEmail = $request->getPut("user_email");
		 *
		 *	$userEmail = $request->getPut("user_email", "email");
		 *</code>
		 *
		 * @param string $name
		 * @param string|array $filters
		 * @param mixed $defaultValue
		 * @param boolean $notAllowEmpty
		 * @return mixed
		 */
		public function getPut($name=null, $filters=null, $defaultValue=null, $notAllowEmpty=false){
			return $this->_getHelper($this->_putCache,$name,$filters,$defaultValue,$notAllowEmpty);
		}


		/**
		 * Gets variable from $_GET superglobal applying filters if needed
		 * If no parameters are given the $_GET superglobal is returned
		 *
		 *<code>
		 *	//Returns value from $_GET["id"] without sanitizing
		 *	$id = $request->getQuery("id");
		 *
		 *	//Returns value from $_GET["id"] with sanitizing
		 *	$id = $request->getQuery("id", "int");
		 *
		 *	//Returns value from $_GET["id"] with a default value
		 *	$id = $request->getQuery("id", null, 150);
		 *</code>
		 *
		 * @param string $name
		 * @param string|array $filters
		 * @param mixed $defaultValue
		 * @param boolean $notAllowEmpty
		 * @return mixed
		 */
		public function getQuery($name=null, $filters=null, $defaultValue=null,$notAllowEmpty=false){
			return $this->_getHelper($_GET,$name,$filters,$defaultValue,$notAllowEmpty);
		}


		/**
		 * Checks whether $_REQUEST superglobal has certain index
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function has($name){
			return isset($_REQUEST[$name]);
		}


		/**
		 * Checks whether $_GET superglobal has certain index
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function hasGet($name){
			return isset($_GET[$name]);
		}

		/**
		 * Checks whether $_POST superglobal has certain index
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function hasPost($name){
			return isset($_POST[$name]);
		}


		/**
		 * Checks whether put has certain index
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function hasPut($name){
			return isset($this->_putCache[$name]);
		}


		/**
		 * Checks whether $_GET superglobal has certain index
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function hasQuery($name){
			return isset($_GET[$name]);
		}


		/**
		 * Gets HTTP schema (http/https)
		 *
		 * @return string
		 */
		public function getScheme(){
			if(isset($_SERVER['REQUEST_SCHEME'])){
				return $_SERVER['REQUEST_SCHEME'];
			}else{
				return '';
			}
		}


		/**
		 * Checks whether request has been made using ajax. Checks if $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'
		 *
		 * @return boolean
		 */
		public function isAjax(){
			return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] === "XMLHttpRequest";
		}


		/**
		 * Checks whether request has been made using any secure layer
		 *
		 * @return boolean
		 */
		public function isSecureRequest(){
			return $this->getScheme()==='https';
		}


		/**
		 * Gets HTTP raw request body
		 *
		 * @return string
		 */
		public function getRawBody(){
			if(empty($this->_rawBody)){
				$this->_rawBody =file_get_contents("php://input");
			}

			return $this->_rawBody;
		}


		/**
		 * Gets most possible client IPv4 Address. This method search in $_SERVER['REMOTE_ADDR'] and optionally in $_SERVER['HTTP_X_FORWARDED_FOR']
		 *
		 * @param boolean $trustForwardedHeader
		 * @return string
		 */
		public function getClientAddress($trustForwardedHeader=false){
			$address =null;
			if($trustForwardedHeader){
				if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
					$address=$_SERVER['HTTP_X_FORWARDED_FOR'];
				}else{
					$address=$_SERVER['HTTP_CLIENT_IP'];
				}
			}

			if($address ===null){
				$address =$_SERVER['REMOTE_ADDR'];
			}

			if(is_string($address)){
				if(strpos($address,',') !==false){
					return strstr($address,',',true);
				}else{
					return $address;
				}
			}else{
				return false;
			}
		}


		/**
		 * Gets HTTP user agent used to made the request
		 *
		 * @return string
		 */
		public function getUserAgent(){
			return isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
		}


		/**
		 * Checks whether HTTP method is POST. if $_SERVER['REQUEST_METHOD']=='POST'
		 *
		 * @return boolean
		 */
		public function isPost(){
			return $_SERVER['REQUEST_METHOD'] ==='POST';
		}


		/**
		 * Checks whether HTTP method is GET. if $_SERVER['REQUEST_METHOD']=='GET'
		 *
		 * @return boolean
		 */
		public function isGet(){
			return $_SERVER['REQUEST_METHOD'] ==='GET';
		}


		/**
		 * Checks whether HTTP method is PUT. if $_SERVER['REQUEST_METHOD']=='PUT'
		 *
		 * @return boolean
		 */
		public function isPut(){
			return $_SERVER['REQUEST_METHOD'] ==='PUT';
		}


		/**
		 * Checks whether HTTP method is PATCH. if $_SERVER['REQUEST_METHOD']=='PATCH'
		 *
		 * @return boolean
		 */
		public function isPatch(){
			return $_SERVER['REQUEST_METHOD'] ==='PATCH';
		}


		/**
		 * Checks whether HTTP method is HEAD. if $_SERVER['REQUEST_METHOD']=='HEAD'
		 *
		 * @return boolean
		 */
		public function isHead(){
			return $_SERVER['REQUEST_METHOD'] ==='HEAD';
		}


		/**
		 * Checks whether HTTP method is DELETE. if $_SERVER['REQUEST_METHOD']=='DELETE'
		 *
		 * @return boolean
		 */
		public function isDelete(){
			return $_SERVER['REQUEST_METHOD'] ==='DELETE';
		}


		/**
		 * Checks whether HTTP method is OPTIONS. if $_SERVER['REQUEST_METHOD']=='OPTIONS'
		 *
		 * @return boolean
		 */
		public function isOptions(){
			return $_SERVER['REQUEST_METHOD'] ==='OPTIONS';
		}


		/**
		 * Checks whether request includes attached files
		 * @param boolean $onlySuccessful
		 * @return boolean
		 */
		public function hasFiles($onlySuccessful=false){
			if($this->_files ===null){
				$this->_getFilesHelper($onlySuccessful);
			}

			return count($this->_files)>1;
		}

		protected function _getFilesHelper($onlySuccessful){
			throw new Exception('not support hasFiles and getUploadedFiles api '. $onlySuccessful);
		}

		/**
		 * Gets attached files as \ManaPHP\Http\Request\File instances
		 *
		 * @param boolean $onlySuccessful
		 * @return \ManaPHP\Http\Request\File[]
		 */
		public function getUploadedFiles($onlySuccessful=null){
			if($this->_files ===null){
				$this->_getFilesHelper($onlySuccessful);
			}

			return $this->_files;
		}


		/**
		 * Gets web page that refers active request. ie: http://www.google.com
		 *
		 * @return string
		 */
		public function getHTTPReferer(){
			return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		}
	}
}
