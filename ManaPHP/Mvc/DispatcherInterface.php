<?php

namespace ManaPHP\Mvc {

    /**
     * ManaPHP\Mvc\DispatcherInterface initializer
     */
    interface DispatcherInterface
    {
        /**
         * Sets the default namespace
         *
         * @param string $namespace
         * @return static
         */
        public function setDefaultNamespace($namespace);

        /**
         * Returns the default namespace
         *
         * @return string
         */
        public function getDefaultNamespace();

        /**
         * Sets the namespace which the controller belongs to
         * @param string $namespaceName
         * @return static
         */
        public function setNamespaceName($namespaceName);

        /**
         * Sets the module name which the application belongs to
         * @param string $moduleName
         * @return static
         */
        public function setModuleName($moduleName);


        /**
         * Sets the action name to be dispatched
         *
         * @param string $actionName
         * @return static
         */
        public function setActionName($actionName);


        /**
         * Gets last dispatched action name
         *
         * @return string
         */
        public function getActionName();


        /**
         * Sets action params to be dispatched
         *
         * @param array $params
         * @return static
         */
        public function setParams($params);


        /**
         * Gets action params
         *
         * @return array
         */
        public function getParams();


        /**
         * Set a param by its name or numeric index
         *
         * @param  string|int $param
         * @param  mixed $value
         * @return static
         */
        public function setParam($param, $value);


        /**
         * Gets a param by its name or numeric index
         *
         * @param  string|int $param
         * @param  string|array $filters
         * @param mixed $defaultValue
         * @return mixed
         */
        public function getParam($param, $filters = null, $defaultValue = null);


        /**
         * Returns value returned by the latest dispatched action
         *
         * @return mixed
         */
        public function getReturnedValue();


        /**
         * Dispatches a handle action taking into account the routing parameters
         *
         * @return false|\ManaPHP\Mvc\ControllerInterface
         */
        public function dispatch();


        /**
         * Forwards the execution flow to another controller/action
         *
         * @param array $forward
         */
        public function forward($forward);

        /**
         * Check if the current executed action was forwarded by another one
         *
         * @return boolean
         */
        public function wasForwarded();


        /**
         * Sets the controller name to be dispatched
         *
         * @param string $controllerName
         */
        public function setControllerName($controllerName);


        /**
         * Gets last dispatched controller name
         *
         * @return string
         */
        public function getControllerName();


        /**
         * Returns the previous controller in the dispatcher
         *
         * @return string
         */
        public function getPreviousControllerName();

        /**
         * Returns the previous action in the dispatcher
         *
         * @return string
         */
        public function getPreviousActionName();
    }
}
