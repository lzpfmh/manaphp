<?php

namespace ManaPHP\Mvc {

    /**
     * ManaPHP\Mvc\RouterInterface initializer
     *
     * PHP_NOTE:
     *        1. remove the clear method
     */
    interface RouterInterface
    {

        /**
         * Sets the name of the default module
         *
         * @param string $moduleName
         */
        public function setDefaultModule($moduleName);


        /**
         * Returns the name of the default module
         *
         * @return string
         */
        public function getDefaultModule();

        /**
         * Sets the default controller name
         *
         * @param string $controllerName
         */
        public function setDefaultController($controllerName);


        /**
         * Returns the default controller name
         *
         * @return string
         */
        public function getDefaultController();


        /**
         * Sets the default action name
         *
         * @param string $actionName
         */
        public function setDefaultAction($actionName);

        /**
         * Returns the default action name
         *
         * @return string
         */
        public function getDefaultAction();


        /**
         * Handles routing information received from the rewrite engine
         *
         * @param string $uri
         * @return boolean
         */
        public function handle($uri = null);


        /**
         * Adds a route to the router on any HTTP method
         *
         * @param string $pattern
         * @param array $paths
         * @param string $httpMethods
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function add($pattern, $paths, $httpMethods = null);


        /**
         * Adds a route to the router that only match if the HTTP method is GET
         *
         * @param string $pattern
         * @param array $paths
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function addGet($pattern, $paths);


        /**
         * Adds a route to the router that only match if the HTTP method is POST
         *
         * @param string $pattern
         * @param array $paths
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function addPost($pattern, $paths);


        /**
         * Adds a route to the router that only match if the HTTP method is PUT
         *
         * @param string $pattern
         * @param array $paths
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function addPut($pattern, $paths);


        /**
         * Adds a route to the router that only match if the HTTP method is DELETE
         *
         * @param string $pattern
         * @param array $paths
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function addDelete($pattern, $paths);


        /**
         * Add a route to the router that only match if the HTTP method is OPTIONS
         *
         * @param string $pattern
         * @param array $paths
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function addOptions($pattern, $paths);


        /**
         * Add a route to the router that only match if the HTTP method is PATCH
         *
         * @param string $pattern
         * @param array $paths
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function addPatch($pattern, $paths);


        /**
         * Adds a route to the router that only match if the HTTP method is HEAD
         *
         * @param string $pattern
         * @param array $paths
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function addHead($pattern, $paths);

        /**
         * Mounts a group of routes in the router
         * @param \ManaPHP\Mvc\Router\GroupInterface
         * @return  static
         */
        public function mount($group);


        /**
         * Returns processed module name
         *
         * @return string
         */
        public function getModuleName();


        /**
         * Returns processed namespace name
         *
         * @return string
         */
        public function getNamespaceName();

        /**
         * Returns processed controller name
         *
         * @return string
         */
        public function getControllerName();


        /**
         * Returns processed action name
         *
         * @return string
         */
        public function getActionName();


        /**
         * Returns processed extra params
         *
         * @return array
         */
        public function getParams();


        /**
         * Returns the route that matches the handled URI
         *
         * @return \ManaPHP\Mvc\Router\RouteInterface
         */
        public function getMatchedRoute();


        /**
         * Check if the router matches any of the defined routes
         *
         * @return bool
         */
        public function wasMatched();


        /**
         * Return all the routes defined in the router
         *
         * @return \ManaPHP\Mvc\Router\RouteInterface[]
         */
        public function getRoutes();
    }
}
