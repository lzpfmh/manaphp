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
            foreach ($namespaces as $namespace => $path) {
                $path = rtrim($path, '\\/');
                if (DIRECTORY_SEPARATOR === '\\') {
                    $path = str_replace('\\', '/', $path);
                }
                $namespaces[$namespace] = $path;
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
            foreach ($directories as $key => $directory) {
                $directory = rtrim($directory, '\\/');
                if (DIRECTORY_SEPARATOR === '\\') {
                    $directory = str_replace('\\', '/', $directory);
                }
                $directories[$key] = $directory;
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
            if (DIRECTORY_SEPARATOR === '\\') {
                foreach ($classes as $class => $path) {
                    $classes[$class] = str_replace('\\', '/', $path);
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
                spl_autoload_register([$this, '___autoload']);
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
                spl_autoload_unregister([$this, '___autoload']);
                $this->_registered = false;
            }
            return $this;
        }


        /**
         * If a file exists, require it from the file system.
         *
         * @param string $file The file to require.
         */
        protected function ___requireFile($file)
        {
            if (file_exists($file)) {
                if (DIRECTORY_SEPARATOR === '\\') {
                    $realPath = str_replace('\\', '/', realpath($file));
                    if ($realPath !== $file) {
                        trigger_error("File name ($realPath) case mismatch for .$file", E_USER_ERROR);
                    }
                }
                /** @noinspection PhpIncludeInspection */
                require $file;
            }
        }

        /**
         * Makes the work of autoload registered classes
         *
         * @param string $className
         * @return bool
         */
        protected function ___autoload($className)
        {
            if (is_array($this->_classes) && isset($this->_classes[$className])) {
                $this->___requireFile($this->_classes[$className]);
                return true;
            }

            if (is_array($this->_namespaces)) {
                /** @noinspection LoopWhichDoesNotLoopInspection */
                foreach ($this->_namespaces as $namespace => $directory) {
                    if (strpos($className, $namespace) !== 0) {
                        continue;
                    }
                    $len = strlen($namespace);
                    $file = $directory . str_replace('\\', '/', substr($className, $len)) . '.php';
                    $this->___requireFile($file);
                    return true;
                }
            }

            if (is_array($this->_directories)) {
                foreach ($this->_directories as $directory) {
                    $file = $directory . basename($className) . '.php';
                    $file = str_replace('\\', '/', $file);
                    if (file_exists($file)) {
                        $this->___requireFile($file);
                        return true;
                    }
                }
            }

            return false;
        }
    }
}
