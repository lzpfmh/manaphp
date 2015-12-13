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

	class Autoloader{

		/**
		 * @var \ManaPHP\Events\ManagerInterface
		 */
		protected $_eventsManager;

		/**
		 * @var array
		 */
		protected $_classes=[];

		/**
		 * @var array
		 */
		protected $_namespaces=[];

		/**
		 * @var array
		 */
		protected $_directories=[];

		/**
		 * @var boolean
		 */
		protected $_registered=false;

		/**
		 * @var string|boolean
		 */
		protected $_requiredFile=false;

		/**
		 * Sets the events manager
		 *
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 * @return static
		 */
		public function setEventsManager($eventsManager){
			$this->_eventsManager =$eventsManager;
			return $this;
		}


		/**
		 * Returns the internal event manager
		 *
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		public function getEventsManager(){
			return $this->_eventsManager;
		}


		/**
		 * Register namespaces and their related directories
		 *
		 * <code>
		 * $loader->registerNamespaces(array(
		 * 		’Example\\Base’ => ’vendor/example/base/’,
		 *		’Example\\Adapter’ => ’vendor/example/adapter/’,
		 *		’Example’ => ’vendor/example/’
		 *		));
		 * </code>
		 * @param array $namespaces
		 * @param boolean $merge
		 * @return static
		 */
		public function registerNamespaces($namespaces, $merge=false){
			if($merge ===false){
				$this->_namespaces =$namespaces;
			}else{
				$this->_namespaces=is_array($this->_namespaces)?array_merge($this->_namespaces,$namespaces):$namespaces;
			}

			return $this;
		}


		/**
		 * Return current namespaces registered in the autoloader
		 *
		 * @return array
		 */
		public function getNamespaces(){
			return $this->_namespaces;
		}


		/**
		 * Register directories on which "not found" classes could be found
		 *
		 * <code>
		 * $loader->registerDirs(
		 *			array(
		 *				__DIR__ . ’/models/’,
		 *				));
		 * </code>
		 * @param array $directories
		 * @param boolean $merge
		 * @return static
		 */
		public function registerDirs($directories, $merge=false){
			if($merge ===false){
				$this->_directories =$directories;
			}else{
				$this->_directories=is_array($this->_directories)?array_merge($this->_directories,$directories):$directories;
			}
			return $this;
		}


		/**
		 * Return current directories registered in the autoloader
		 *
		 * @return array
		 */
		public function getDirs(){
			return $this->_directories;
		}


		/**
		 * Register classes and their locations
		 *
		 * @param array $classes
		 * @param boolean $merge
		 * @return static
		 */
		public function registerClasses($classes, $merge=false){
			if($merge ===false){
				$this->_classes =$classes;
			}else{
				$this->_classes=is_array($this->_classes)?array_merge($this->_classes,$classes):$classes;
			}

			return $this;
		}


		/**
		 * Return the current class-map registered in the autoloader
		 *
		 * @return array
		 */
		public function getClasses(){
			return $this->_classes;
		}


		/**
		 * Register the autoload method
		 *
		 * @return static
		 */
		public function register(){
			if($this->_registered ===false){
				spl_autoload_register([$this,'_autoload']);
				$this->_registered=true;
			}

			return $this;
		}


		/**
		 * Unregister the autoload method
		 *
		 * @return static
		 */
		public function unregister(){
			if($this->_registered ===true){
				spl_autoload_unregister([$this,'_autoLoad']);
				$this->_registered =false;
			}
			return $this;
		}


		/**
		 * If a file exists, require it from the file system.
		 *
		 * @param string $file The file to require.
		 * @return bool True if the file exists, false if not.
		 */
		protected function _requireFile($file)
		{
			if (file_exists($file)) {

				/** @noinspection PhpIncludeInspection */
				require $file;
				return true;
			}
			return false;
		}

		/**
		 * get the latest loaded file path
		 * @return string
		 */
		public function getRequiredFile(){
			return $this->_requiredFile;
		}

		/**
		 * Makes the work of autoload registered classes
		 *
		 * @param string $className
		 * @return boolean
		 */
		protected function _autoLoad($className){
			$this->_requiredFile=false;

			if(is_array($this->_classes)){
				if(isset($this->_classes[$className])){
					$this->_requiredFile =$this->_classes[$className];
					return $this->_requireFile($this->_classes[$className]);
				}
			}

			if(is_array($this->_namespaces)){
				/** @noinspection LoopWhichDoesNotLoopInspection */
				foreach($this->_namespaces as $namespace=>$directory){
					$len =strlen($namespace);
					if(strncmp($namespace,$className,$len) !==0){
						continue;
					}
					$file=$directory.substr($className,$len).'.php';
					$file =str_replace('\\','/',$file);
					$this->_requiredFile=$file;
					return $this->_requireFile($file);
				}
			}

			if(is_array($this->_directories)){
				foreach($this->_directories as $directory){
					$file =$directory.basename($className).'.php';
					$file =str_replace('\\','/',$file);
					$r=$this->_requireFile($file);
					if($r ===true){
						$this->_requiredFile =$file;
						return true;
					}
				}
			}

			return false;
		}

		public static function autoloadFrameWorkClasses(){
			static $has_registered=false;

			if($has_registered){
				return;
			}

			spl_autoload_register(function($className){

				static $frameworkRootPath;
				static $frameworkName;

				if(!isset($frameworkRootPath)){
					$frameworkRootPath=__DIR__;
					$frameworkName=basename($frameworkRootPath);
					$frameworkRootPath=dirname($frameworkRootPath);
				}

////				echo $className.'<br/>';
//				if(strpos($className,'Interface') !==false){
//					//create_function('','interface '.$className.' {}');
//					eval('namespace '.str_replace('/','\\',dirname(str_replace('\\',DIRECTORY_SEPARATOR,$className))).'{interface ' . basename(str_replace('\\',DIRECTORY_SEPARATOR,$className)) . ' {}}');
//					return true;
//				}
				if(strncmp($className,$frameworkName, strlen($frameworkName)) ===0){
					$file =$frameworkRootPath.'/'.$className.'.php';
					$file =str_replace('\\','/',$file);
					if(is_file($file)){

						/** @noinspection PhpIncludeInspection */
						require $file;
						return true;
					}
				}

				return false;
			});
		}
	}
}
