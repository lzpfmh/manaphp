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
         *
         * @param \ManaPHP\Mvc\Router\GroupInterface $group
         * @param string $module
         * @return  static
         */
        public function mount($group,$module=null);

        /**
         * Set whether router must remove the extra slashes in the handled routes
         *
         * @param boolean $remove
         * @return static
         */
        public function removeExtraSlashes($remove);

        /**
         * Get rewrite info. This info is read from $_GET['_url']. This returns '/' if the rewrite information cannot be read
         *
         * @return string
         * @throws \ManaPHP\Mvc\Router\Exception
         */
        public function getRewriteUri();


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
         * Check if the router matches any of the defined routes
         *
         * @return bool
         */
        public function wasMatched();
    }
}
