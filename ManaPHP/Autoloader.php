<?php

namespace ManaPHP {

    class Autoloader
    {
        protected static $_rootPath;
        protected static $_optimizeMode;

        public static function autoload($className)
        {
            if (strncmp($className, 'ManaPHP', 7) !== 0) {
                return false;
            }

            if (self::$_rootPath === null) {
                self::$_rootPath = str_replace('\\','/',dirname(__DIR__));
            }

            if (self::$_optimizeMode && substr_compare($className, 'Interface', strlen($className) - 9) === 0) {
                $namespaceName = str_replace('/', '\\', dirname(str_replace('\\', DIRECTORY_SEPARATOR, $className)));
                $interfaceName = basename(str_replace('\\', DIRECTORY_SEPARATOR, $className));

                eval('namespace ' . $namespaceName . '{interface ' . $interfaceName . ' {}}');
                return true;
            }

            $file = self::$_rootPath . '/' . str_replace('\\','/',$className) . '.php';
            if (is_file($file)) {
                if(DIRECTORY_SEPARATOR==='\\' &&str_replace('\\','/',realpath($file)) !==$file){
                    trigger_error('File name case mismatch for '.$file,E_USER_ERROR);
                }

                /** @noinspection PhpIncludeInspection */
                require $file;
                return true;
            }

            return false;
        }

        /**
         * @param bool|true $optimizeMode
         * @return bool
         */
        public static function register($optimizeMode = true)
        {
            self::$_optimizeMode = $optimizeMode;

            return spl_autoload_register([__CLASS__, 'autoload']);
        }
    }
}
