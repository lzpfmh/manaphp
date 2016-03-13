<?php

namespace ManaPHP\Mvc {

    use ManaPHP\Component;
    use ManaPHP\DiInterface;

    use ManaPHP\Mvc\Dispatcher\Exception;

    /**
     * ManaPHP\Mvc\Dispatcher
     *
     * Dispatching is the process of taking the request object, extracting the module name,
     * controller name, action name, and optional parameters contained in it, and then
     * instantiating a controller and calling an action of that controller.
     *
     *<code>
     *
     *    $di = new ManaPHP\Di();
     *
     *    $dispatcher = new ManaPHP\Mvc\Dispatcher();
     *
     *  $dispatcher->setDI($di);
     *
     *    $dispatcher->setControllerName('posts');
     *    $dispatcher->setActionName('index');
     *    $dispatcher->setParams(array());
     *
     *    $controller = $dispatcher->dispatch();
     *
     *</code>
     */
    class Dispatcher extends Component implements DispatcherInterface
    {
        const EXCEPTION_CONTROLLER_NOT_FOUND = 2;

        const EXCEPTION_INVALID_CONTROLLER = 3;

        const EXCEPTION_ACTION_NOT_FOUND = 5;


        protected $_activeController;

        /**
         * @var boolean
         */
        protected $_finished = false;

        /**
         * @var boolean
         */
        protected $_forwarded = false;

        /**
         * @var string
         */
        protected $_moduleName;

        /**
         * @var string
         */
        protected $_namespaceName;

        /**
         * @var string
         */
        protected $_controllerName;

        /**
         * @var string
         */
        protected $_actionName;

        /**
         * @var array
         */
        protected $_params = [];

        /**
         * @var mixed
         */
        protected $_returnedValue;

        protected $_lastController;

        /**
         * @var string
         */
        protected $_defaultNamespace=null;

        /**
         * @var string
         */
        protected $_defaultController = 'Index';

        /**
         * @var string
         */
        protected $_defaultAction = 'index';

        /**
         * @var string
         */
        protected $_controllerSuffix = 'Controller';

        /**
         * @var string
         */
        protected $_actionSuffix = 'Action';

        /**
         * @var string
         */
        protected $_previousControllerName;

        /**
         * @var string
         */
        protected $_previousActionName;

        /**
         * \ManaPHP\Dispatcher constructor
         */
        public function __construct()
        {
        }


        /**
         * Sets the module where the controller is (only informative)
         *
         * @param string $moduleName
         * @return static
         */
        public function setModuleName($moduleName)
        {
            $this->_moduleName = $this->_camelize($moduleName);

            return $this;
        }


        /**
         * Gets the module where the controller class is
         *
         * @return string
         */
        public function getModuleName()
        {
            return $this->_moduleName;
        }


        /**
         * Sets the namespace where the controller class is
         *
         * @param string $namespaceName
         * @return static
         */
        public function setNamespaceName($namespaceName)
        {
            $this->_namespaceName = $namespaceName;
            return $this;
        }


        /**
         * Gets a namespace to be prepended to the current handler name
         *
         * @return string
         */
        public function getNamespaceName()
        {
            return $this->_namespaceName;
        }


        /**
         * Sets the default namespace
         *
         * @param string $namespace
         * @return static
         */
        public function setDefaultNamespace($namespace)
        {
            $this->_defaultNamespace = $namespace;
            return $this;
        }


        /**
         * Returns the default namespace
         *
         * @return string
         */
        public function getDefaultNamespace()
        {
            return $this->_defaultNamespace;
        }


        /**
         * Sets the action name to be dispatched
         *
         * @param string $actionName
         * @return static
         */
        public function setActionName($actionName)
        {
            $this->_actionName = lcfirst($actionName);
            return $this;
        }


        /**
         * Gets the latest dispatched action name
         *
         * @return string
         */
        public function getActionName()
        {
            return $this->_actionName;
        }


        /**
         * Sets action params to be dispatched
         *
         * @param array $params
         * @return static
         * @throws \ManaPHP\Mvc\Dispatcher\Exception
         */
        public function setParams($params)
        {
            if (!is_array($params)) {
                throw new Exception('Parameters must be an Array');
            }
            $this->_params = $params;

            return $this;
        }


        /**
         * Gets action params
         *
         * @return array
         */
        public function getParams()
        {
            return $this->_params;
        }


        /**
         * Set a param by its name or numeric index
         *
         * @param  string|int $param
         * @param  mixed $value
         * @return static
         */
        public function setParam($param, $value)
        {
            $this->_params[$param] = $value;
            return $this;
        }


        /**
         * Gets a param by its name or numeric index
         *
         * @param  string|int $param
         * @param  string|array $filters
         * @param  mixed $defaultValue
         * @return mixed
         * @throws \ManaPHP\Mvc\Dispatcher\Exception
         */
        public function getParam($param, $filters = null, $defaultValue = null)
        {
            if (!isset($this->_params[$param])) {
                return $defaultValue;
            }

            if ($filters === null) {
                return $this->_params[$param];
            }

            if (!is_object($this->_dependencyInjector)) {
                throw new Exception("A dependency injection object is required to access the 'filter' service");
            }

            return null;
            /*
             * todo
             */
            //	return $this->_dependencyInjector->getShared('filter')->sanitize($this->_params[$param],$filters);
        }


        /**
         * Sets the latest returned value by an action manually
         *
         * @param mixed $value
         * @return static
         */
        public function setReturnedValue($value)
        {
            $this->_returnedValue = $value;
            return $this;
        }


        /**
         * Returns value returned by the latest dispatched action
         *
         * @return mixed
         */
        public function getReturnedValue()
        {
            return $this->_returnedValue;
        }


        /**
         * Dispatches a handle action taking into account the routing parameters
         *
         * @return false|\ManaPHP\Mvc\ControllerInterface
         * @throws \ManaPHP\Mvc\Dispatcher\Exception
         */
        public function dispatch()
        {
            if (!$this->_dependencyInjector instanceof DiInterface) {
                throw new Exception('A dependency injection container is required to access related dispatching services');
            }

            if ($this->fireEvent('dispatcher:beforeDispatchLoop', $this) === false) {
                return false;
            }

            $controller = null;
            $numberDispatches = 0;
            $this->_finished = false;
            while ($this->_finished === false) {
                // if the user made a forward in the listener,the $this->_finished will be changed to false.
                $this->_finished = true;

                if ($numberDispatches++ === 32) {
                    throw new Exception('Dispatcher has detected a cyclic routing causing stability problems');
                }

                $this->_resolveEmptyProperties();

                $this->fireEvent('dispatcher:beforeDispatch', $this);

                if ($this->_finished === false) {
                    continue;
                }

                $controllerClass = $this->_getControllerClass();

                if (!$this->_dependencyInjector->has($controllerClass) && !class_exists($controllerClass)) {
                    if($this->fireEvent('dispatcher:beforeNotFoundController',$this)===false){
                        return false;
                    }

                    if($this->_finished ===false){
                        continue;
                    }

                    throw new Exception($controllerClass.' handler class cannot be loaded');
                }

                $controller = $this->_dependencyInjector->getShared($controllerClass);
                $wasFreshInstance = $this->_dependencyInjector->wasFreshInstance();
                if (!is_object($controller)) {
                    throw new Exception('Invalid handler type returned from the services container: '.gettype($controller));
                }

                $this->_activeController = $controller;

                $actionMethod = $this->_actionName . $this->_actionSuffix;
                if (!method_exists($controller, $actionMethod)) {
                    if ($this->fireEvent('dispatcher:beforeNotFoundAction', $this) === false) {
                        continue;
                    }

                    if ($this->_finished === false) {
                        continue;
                    }

                    throw new Exception('Action \'' . $this->_actionName . '\' was not found on handler \'' . $controllerClass . '\'');
                }

                // Calling beforeExecuteRoute as callback
                if (method_exists($controller, 'beforeExecuteRoute')) {
                    if ($controller->beforeExecuteRoute($this) === false) {
                        continue;
                    }

                    if ($this->_finished === false) {
                        continue;
                    }
                }

                if ($wasFreshInstance) {
                    if (method_exists($controller, 'initialize')) {
                        $controller->initialize();
                    }
                }

                $this->_returnedValue = call_user_func_array([$controller, $actionMethod], $this->_params);
                $this->_lastController = $controller;

                $value = null;

                // Call afterDispatch
                $this->fireEvent('dispatcher:afterDispatch', $this);

                if (method_exists($controller, 'afterExecuteRoute')) {
                    if ($controller->afterExecuteRoute($this, $value) === false) {
                        continue;
                    }

                    if ($this->_finished === false) {
                        continue;
                    }
                }
            }

            $this->fireEvent('dispatcher:afterDispatchLoop', $this);

            return $controller;
        }


        /**
         * Forwards the execution flow to another controller/action
         * Dispatchers are unique per module. Forwarding between modules is not allowed
         *
         *<code>
         *  $this->dispatcher->forward(array('controller' => 'posts', 'action' => 'index'));
         *</code>
         *
         * @param array $forward
         * @throws \ManaPHP\Mvc\Dispatcher\Exception
         */
        public function forward($forward)
        {
            if (!is_array($forward)) {
                throw new Exception('Forward parameter must be an Array');
            }
            
            if (isset($forward['namespace'])) {
                $this->_namespaceName = $forward['namespace'];
            }

            if (isset($forward['controller'])) {
                $this->_previousControllerName = $this->_controllerName;
                $this->_controllerName = $this->_camelize($forward['controller']);
            }

            if (isset($forward['action'])) {
                $this->_previousActionName = $this->_actionName;
                $this->_actionName = lcfirst($forward['action']);
            }

            if (isset($forward['params'])) {
                $this->_params = $forward['params'];
            }

            $this->_finished = false;
            $this->_forwarded = true;
        }


        /**
         * Check if the current executed action was forwarded by another one
         *
         * @return boolean
         */
        public function wasForwarded()
        {
            return $this->_forwarded;
        }


        /**
         * @param string $str
         * @return string
         */
        protected function _camelize($str)
        {
            if(strpos($str,'_') !==false){
                $parts = explode('_', $str);
                foreach ($parts as $k => $v) {
                    $parts[$k] = ucfirst($v);
                }

                return implode('', $parts);
            }else{
                return ucfirst($str);
            }
        }

        /**
         * Possible class name that will be located to dispatch the request
         *
         * @return string
         */
        protected function _getControllerClass()
        {
            $this->_resolveEmptyProperties();

            $camelizedClass = $this->_controllerName;

            if ($this->_namespaceName) {
                $handlerClass = rtrim($this->_namespaceName, '\\') . '\\' . $camelizedClass . $this->_controllerSuffix;
            } else {
                $handlerClass = $camelizedClass . $this->_controllerSuffix;
            }

            return $handlerClass;
        }

        /**
         * Set empty properties to their defaults (where defaults are available)
         */
        protected function _resolveEmptyProperties()
        {
            // If the current namespace is null we used the set in this->_defaultNamespace
            if ($this->_namespaceName === null) {
                $this->_namespaceName = $this->_defaultNamespace;
            }

            // If the handler is null we use the set in this->_defaultHandler
            if ($this->_controllerName === null) {
                $this->_controllerName = $this->_defaultController;
            }

            // If the action is null we use the set in this->_defaultAction
            if ($this->_actionName === null) {
                $this->_actionName = $this->_defaultAction;
            }
        }


        /**
         * Sets the controller name to be dispatched
         *
         * @param string $controllerName
         */
        public function setControllerName($controllerName)
        {
            $this->_controllerName = $this->_camelize($controllerName);
        }


        /**
         * Gets last dispatched controller name
         *
         * @return string
         */
        public function getControllerName()
        {
            return $this->_controllerName;
        }


        /**
         * Returns the previous controller in the dispatcher
         *
         * @return string
         */
        public function getPreviousControllerName()
        {
            return $this->_previousControllerName;
        }

        /**
         * Returns the previous action in the dispatcher
         *
         * @return string
         */
        public function getPreviousActionName()
        {
            return $this->_previousActionName;
        }

        /**
         * Throws an internal exception
         *
         * @param string $message
         * @param int $exceptionCode
         * @return boolean
         * @throws \ManaPHP\Mvc\Dispatcher\Exception
         */
        protected function _throwDispatchException($message, $exceptionCode = 0)
        {
            if (!is_object($this->_dependencyInjector)) {
                throw new Exception("A dependency injection container is required to access the 'response' service");
            }

            $response = $this->_dependencyInjector->getShared('response');
            $response->setStatusCode(404, 'Not Found');

            $exception = new Exception($message, $exceptionCode);

            if ($this->_handleException($exception) === false) {
                return false;
            }

            throw $exception;
        }


        /**
         * Handles a user exception
         *
         * @param \Exception $exception
         * @return boolean
         *
         * @warning If any additional logic is to be implemented here, please check
         * ManaPHP_dispatcher_fire_event() first
         */
        protected function _handleException($exception)
        {

        }
    }
}
