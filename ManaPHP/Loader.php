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
    class Loader
    {
        /**
         * @var array
         */
        protected $_classes = [];

        /**
         * @var array
         */
        protected $_namespaces = [];

        /**
         * @var array
         */
        protected $_directories = [];

        /**
         * @var boolean
         */
        protected $_registered = false;

        /**
         * @var string|boolean
         */
        protected $_requiredFile = false;

        /**
         * Register namespaces and their related directories
         *
         * <code>
         * $loader->registerNamespaces(array(
         *        ’Example\\Base’ => ’vendor/example/base/’,
         *        ’Example\\Adapter’ => ’vendor/example/adapter/’,
         *        ’Example’ => ’vendor/example/’
         *        ));
         * </code>
         * @param array $namespaces
         * @param boolean $merge
         * @return static
         */
        public function registerNamespaces($namespaces, $merge = false)
        {
            foreach($namespaces as $namespace=>$path){
                $path=rtrim($path,'\\/');
                if(DIRECTORY_SEPARATOR==='\\'){
                    $path=str_replace('\\','/',$path);
                }
                $namespaces[$namespace]=$path;
            }

            if ($merge === false || $this->_namespaces === null) {
                $this->_namespaces = $namespaces;
            } else {
                $this->_namespaces = array_merge($this->_namespaces, $namespaces);
            }

            return $this;
        }


        /**
         * Return current namespaces registered in the autoloader
         *
         * @return array
         */
        public function getNamespaces()
        {
            return $this->_namespaces;
        }


        /**
         * Register directories on which "not found" classes could be found
         *
         * <code>
         * $loader->registerDirs(
         *            array(
         *                __DIR__ . ’/models/’,
         *                ));
         * </code>
         * @param array $directories
         * @param boolean $merge
         * @return static
         */
        public function registerDirs($directories, $merge = false)
        {
            foreach($directories as $key=>$directory){
                $directory=rtrim($directory,'\\/');
                if(DIRECTORY_SEPARATOR ==='\\'){
                    $directory=str_replace('\\','/',$directory);
                }
                $directories[$key]=$directory;
            }

            if ($merge === false || $this->_directories === null) {
                $this->_directories = $directories;
            } else {
                $this->_directories = array_merge($this->_directories, $directories);
            }
            return $this;
        }


        /**
         * Return current directories registered in the autoloader
         *
         * @return array
         */
        public function getDirs()
        {
            return $this->_directories;
        }


        /**
         * Register classes and their locations
         *
         * @param array $classes
         * @param boolean $merge
         * @return static
         */
        public function registerClasses($classes, $merge = false)
        {
            if(DIRECTORY_SEPARATOR ==='\\'){
                foreach($classes as $class=>$path){
                    $classes[$class]=str_replace('\\','/',$path);
                }
            }

            if ($merge === false || $this->_classes === null) {
                $this->_classes = $classes;
            } else {
                $this->_classes = array_merge($this->_classes, $classes);
            }

            return $this;
        }


        /**
         * Return the current class-map registered in the autoloader
         *
         * @return array
         */
        public function getClasses()
        {
            return $this->_classes;
        }


        /**
         * Register the autoload method
         *
         * @return static
         */
        public function register()
        {
            if ($this->_registered === false) {
                spl_autoload_register([$this, '_autoload']);
                $this->_registered = true;
            }

            return $this;
        }


        /**
         * Unregister the autoload method
         *
         * @return static
         */
        public function unregister()
        {
            if ($this->_registered === true) {
                spl_autoload_unregister([$this, '_autoLoad']);
                $this->_registered = false;
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
                if(DIRECTORY_SEPARATOR==='\\') {
                    $realpath = str_replace('\\', '/', realpath($file));
                    if ($realpath !== $file) {
                        trigger_error("File name ($realpath) case mismatch for .$file", E_USER_ERROR);
                    }
                }
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
        public function getRequiredFile()
        {
            return $this->_requiredFile;
        }

        /**
         * Makes the work of autoload registered classes
         *
         * @param string $className
         * @return boolean
         */
        protected function _autoLoad($className)
        {
            $this->_requiredFile = false;

            if (is_array($this->_classes)) {
                if (isset($this->_classes[$className])) {
                    $this->_requiredFile = $this->_classes[$className];
                    return $this->_requireFile($this->_classes[$className]);
                }
            }

            if (is_array($this->_namespaces)) {
                /** @noinspection LoopWhichDoesNotLoopInspection */
                foreach ($this->_namespaces as $namespace => $directory) {
                    $len = strlen($namespace);
                    if (strncmp($namespace, $className, $len) !== 0) {
                        continue;
                    }
                    $file = $directory . str_replace('\\','/',substr($className, $len)) . '.php';
                    $this->_requiredFile = $file;
                    return $this->_requireFile($file);
                }
            }

            if (is_array($this->_directories)) {
                foreach ($this->_directories as $directory) {
                    $file = $directory . basename($className) . '.php';
                    $file = str_replace('\\', '/', $file);
                    $r = $this->_requireFile($file);
                    if ($r === true) {
                        $this->_requiredFile = $file;
                        return true;
                    }
                }
            }

            return false;
        }
    }
}
