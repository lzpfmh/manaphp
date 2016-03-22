<?php

namespace ManaPHP\Di {

    /**
     * ManaPHP\Di\ServiceInterface initializer
     *
     * Represents a service in the services container
     */
    interface ServiceInterface
    {
        /**
         * Phalcon\Di\ServiceInterface
         *
         * @param string $name
         * @param string|callable|object $definition
         * @param boolean $shared
         */
        public function __construct($name, $definition, $shared);

        /**
         * Resolves the service
         *
         * @param array $parameters
         * @param \ManaPHP\DiInterface $dependencyInjector
         * @return object
         */
        public function resolve($parameters = null, $dependencyInjector = null);

        /**
         * Returns true if the service was resolved
         *
         * @return bool
         */
        public function isResolved();
    }
}
