<?php

namespace ManaPHP\Mvc {

    use ManaPHP\Component;
    use ManaPHP\Di\FactoryDefault;
    use ManaPHP\Http\ResponseInterface;
    use ManaPHP\Mvc\Application\Exception;

    /**
     * ManaPHP\Mvc\Application
     *
     * This component encapsulates all the complex operations behind instantiating every component
     * needed and integrating it with the rest to allow the MVC pattern to operate as desired.
     *
     *<code>
     *
     * class Application extends \ManaPHP\Mvc\Application
     * {
     *
     *        /\**
     *         * Register the services here to make them general or register
     *         * in the ModuleDefinition to make them module-specific
     *         *\/
     *        protected function _registerServices()
     *        {
     *
     *        }
     *
     *        /\**
     *         * This method registers all the modules in the application
     *         *\/
     *        public function main()
     *        {
     *            $this->registerModules(array(
     *                'frontend' => array(
     *                    'className' => 'Multiple\Frontend\Module',
     *                    'path' => '../apps/frontend/Module.php'
     *                ),
     *                'backend' => array(
     *                    'className' => 'Multiple\Backend\Module',
     *                    'path' => '../apps/backend/Module.php'
     *                )
     *            ));
     *        }
     *    }
     *
     *    $application = new Application();
     *    $application->main();
     *
     *</code>
     */
    class Application extends Component
    {

        /**
         * @var string
         */
        protected $_defaultModule;

        /**
         * @var array
         */
        protected $_modules = [];

        /**
         * @var boolean
         */
        protected $_implicitView = true;

        /**
         * \ManaPHP\Mvc\Application
         *
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function __construct($dependencyInjector = null)
        {
            if (is_object($dependencyInjector)) {
                $this->_dependencyInjector = $dependencyInjector;
            } else {
                $this->_dependencyInjector = new FactoryDefault();
            }
            $this->_dependencyInjector->set('application', $this, true);
        }


        /**
         * By default. The view is implicitly buffering all the output
         * You can full disable the view component using this method
         *
         * @param boolean $implicitView
         * @return static
         */
        public function useImplicitView($implicitView)
        {
            $this->_implicitView = $implicitView;
            return $this;
        }


        /**
         * Register an array of modules present in the application
         *
         *<code>
         *    $this->registerModules(array(
         *        'frontend' => array(
         *            'className' => 'Multiple\Frontend\Module',
         *            'path' => '../apps/frontend/Module.php'
         *        ),
         *        'backend' => array(
         *            'className' => 'Multiple\Backend\Module',
         *            'path' => '../apps/backend/Module.php'
         *        )
         *    ));
         *</code>
         *
         * @param array $modules
         * @param boolean $merge
         * @return static
         */
        public function registerModules($modules, $merge = false)
        {
            $this->_modules = $merge === false ? $modules : array_merge($this->_modules, $modules);
            return $this;
        }


        /**
         * Return the modules registered in the application
         *
         * @return array
         */
        public function getModules()
        {
            return $this->_modules;
        }

        /**
         * Gets the module definition registered in the application via module name
         *
         * @param string $name
         * @return array|callable
         * @throws \ManaPHP\Mvc\Application\Exception
         */
        public function getModule($name)
        {
            if (!isset($this->_modules[$name])) {
                throw new Exception("Module '" . $name . "' isn't registered in the application container");
            }

            return $this->_modules[$name];
        }

        /**
         * Sets the module name to be used if the router doesn't return a valid module
         *
         * @param string $defaultModule
         * @return static
         */
        public function setDefaultModule($defaultModule)
        {
            $this->_defaultModule = $defaultModule;
            return $this;
        }

        /**
         * Returns the default module name
         *
         * @return string
         */
        public function getDefaultModule()
        {
            return $this->_defaultModule;
        }


        /**
         * Handles a MVC request
         *
         * @param string $uri
         * @return \ManaPHP\Http\ResponseInterface|boolean
         * @throws \ManaPHP\Mvc\Application\Exception|\ManaPHP\Event\Exception
         */
        public function handle($uri = null)
        {
            if (!is_object($this->_dependencyInjector)) {
                throw new Exception('A dependency injection object is required to access internal services');
            }

            if ($this->fireEvent('application:boot', $this) === false) {
                return false;
            }

            $router = $this->_dependencyInjector->getShared('router');

            $router->setDefaultModule($this->_defaultModule);

            $router->handle($uri);

            $moduleName = $router->getModuleName();
            $moduleObject = null;

            if ($moduleName !== null) {
                if ($this->fireEvent('application:beforeStartModule', $this, $moduleName) === false) {
                    return false;
                }

                $module = $this->getModule($moduleName);

                /**
                 * An array module definition contains a path to a module definition class
                 */
                if (is_array($module)) {
                    /**
                     * Class name used to load the module definition
                     */
                    $className = isset($module['className']) ? $module['className'] : 'Module';

                    /**
                     * If developer specify a path try to include the file
                     */
                    if (isset($module['path'])) {
                        if (!class_exists($className, false)) {
                            if (file_exists($module['path'])) {
                                /** @noinspection PhpIncludeInspection */
                                require($module['path']);
                            } else {
                                throw new Exception("Module definition path '" . $module['path'] . "' doesn't exist");
                            }
                        }
                    }

                    /**
                     * @var \ManaPHP\Mvc\ModuleInterface $moduleObject
                     */
                    $moduleObject = $this->_dependencyInjector->get($className);

                    /**
                     * 'registerAutoloaders' and 'registerServices' are automatically called
                     */
                    $moduleObject->registerAutoloaders($this->_dependencyInjector);
                    $moduleObject->registerServices($this->_dependencyInjector);
                } elseif ($module instanceof \Closure) {
                    $moduleObject = call_user_func_array($module, [$this->_dependencyInjector]);
                } else {
                    throw new Exception('Invalid module definition');
                }

                $this->fireEvent('application:afterStartModule', $this, $moduleObject);
            }

            $dispatcher = $this->_dependencyInjector->getShared('dispatcher');
            $dispatcher->setModuleName($router->getModuleName());
            $dispatcher->setNamespaceName($router->getNamespaceName());
            $dispatcher->setControllerName($router->getControllerName());
            $dispatcher->setActionName($router->getActionName());
            $dispatcher->setParams($router->getParams());

            if ($this->fireEvent('application:beforeHandleRequest', $this, $dispatcher) === false) {
                return false;
            }

            $controller = $dispatcher->dispatch();
            if($controller ===false){
                return false;
            }

            $response=$this->_getResponse($dispatcher->getReturnedValue(),$dispatcher->getControllerName(),$dispatcher->getActionName());

            $this->fireEvent('application:afterHandleRequest', $this, $controller);

            $this->fireEvent('application:beforeSendResponse', $this, $response);

            $response->sendHeaders();
            $response->sendCookies();

            return $response;
        }


        /**
         * @param mixed $actionReturnValue
         * @param string $controller
         * @param string $action
         * @return \ManaPHP\Http\ResponseInterface
         * @throws \ManaPHP\Mvc\Application\Exception
         */
        protected function _getResponse($actionReturnValue,$controller,$action){
            if ($actionReturnValue === false) {
                return $this->_dependencyInjector->getShared('response');
            } elseif($actionReturnValue instanceof ResponseInterface){
                return $actionReturnValue;
            }else{
                if($actionReturnValue ===null){
                    $content='';
                }elseif(is_string($actionReturnValue)){
                    $content=$actionReturnValue;
                }else{
                    throw new Exception('the return value of Action is invalid: '.$actionReturnValue);
                }

                $response =$this->_dependencyInjector->getShared('response');

                if ($this->_implicitView === true) {
                    $view = $this->_dependencyInjector->getShared('view');
                    $view->start();
                    $view->setContent($content);
                    $view->renderView($controller, $action);
                    $view->finish();
                    $response->setContent($view->getContent());
                }else{
                    $response->setContent($content);
                }

                return $response;
            }
        }
    }
}
