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
         * <code>
         *
         *  $router->handle();  //==>$router->handle($_GET['_url'],$_SERVER['HTTP_HOST']);
         *
         *  $router->handle('/blog');   //==>$router->handle('/blog',$_SERVER['HTTP_HOST']);
         *
         * $router->handle('/blog','www.manaphp.com');
         *
         * </code>
         * @param string $uri
         * @param string $host
         * @return boolean
         */
        public function handle($uri = null, $host=null);


        /**
         * Mounts a group of routes in the router
         *
         * <code>
         *  $group=new \ManaPHP\Mvc\Router\Group();
         *
         *  $group->addGet('/blog','blog::list');
         *  $group->addGet('/blog/{id:\d+}','blog::detail')
         *
         *  $router=new \ManaPHP\Mvc\Router();
         *  $router->mount($group,'home');
         * </code>
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
