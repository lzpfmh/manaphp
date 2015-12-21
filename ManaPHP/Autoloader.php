<?php 

namespace ManaPHP {

	class Autoloader{
		protected static $rootPath;

		public static function autoload($className){
			if(strncmp($className,'ManaPHP',7) ===0)
			if(self::$rootPath ===null){
				self::$rootPath=dirname(__DIR__);
			}

				////				echo $className.'<br/>';
//				if(strpos($className,'Interface') !==false){
//					//create_function('','interface '.$className.' {}');
//					eval('namespace '.str_replace('/','\\',dirname(str_replace('\\',DIRECTORY_SEPARATOR,$className))).'{interface ' . basename(str_replace('\\',DIRECTORY_SEPARATOR,$className)) . ' {}}');
//					return true;
//				}
				$file =self::$rootPath.'/'.$className.'.php';
				$file =str_replace('\\','/',$file);
				if(is_file($file)){

				/** @noinspection PhpIncludeInspection */
				require $file;
				return true;

			}

			return false;
		}

		public static function register(){
			return spl_autoload_register([__CLASS__,'autoload']);
		}
	}
}
