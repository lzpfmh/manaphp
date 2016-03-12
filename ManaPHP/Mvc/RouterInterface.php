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
         * Handles routing information received from the rewrite engine
         *
         * @param string $uri
         * @return boolean
         */
        public function handle($uri = null);


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
