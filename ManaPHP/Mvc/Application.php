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
        protected $_baseDirectory;

        /**
         * var string
         */
        protected $_baseNamespace;

        /**
         * @var string
         */
        protected $_defaultModule;

        /**
         * @var array
         */
        protected $_modules = null;

        /**
         * @var boolean
         */
        protected $_implicitView = true;

        /**
         * \ManaPHP\Mvc\Application
         *
         * @param string $baseDirectory
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function __construct($baseDirectory, $dependencyInjector = null)
        {
            if (is_object($dependencyInjector)) {
                $this->_dependencyInjector = $dependencyInjector;
            } else {
                $this->_dependencyInjector = new FactoryDefault();
            }
            $this->_dependencyInjector->setShared('application', $this, true);

            $baseDirectory = str_replace('\\', '/', rtrim($baseDirectory, '\\/'));
            $baseNamespace = ucfirst(basename($baseDirectory));

            $this->_baseDirectory = $baseDirectory;
            $this->_baseNamespace = $baseNamespace;
            $this->loader->registerNamespaces([$baseNamespace => $baseDirectory])->register();
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
         *        'frontend' => 'Multiple\Frontend\Module',
         *        'backend' => 'Multiple\Backend\Module'));
         *</code>
         *
         * @param array $modules
         * @return static
         * @throws \ManaPHP\Mvc\Application\Exception
         */
        public function registerModules($modules = null)
        {
            if ($modules === null) {
                $this->_modules = ['' => $this->_baseNamespace . '\\Module'];
                $this->_defaultModule = '';
            } else {
                foreach ($modules as $module => $definition) {
                    if (is_string($module)) {
                        $moduleName = ucfirst($module);
                        $this->_modules[$moduleName] = $definition;
                    } else {
                        $moduleName = ucfirst($definition);
                        $this->_modules[$moduleName] = $this->_baseNamespace . "\\$moduleName\\Module";
                    }

                    if ($this->_defaultModule === null) {
                        $this->_defaultModule = $moduleName;
                    }
                }
            }

            return $this;
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
            if ($this->fireEvent('application:boot', $this) === false) {
                return false;
            }

            $router = $this->_dependencyInjector->getShared('router');

            $router->handle($uri);

            if ($this->_modules === null) {
                throw new Exception('modules is empty. please register it first.');
            }

            $moduleName = $router->getModuleName();
            if ($moduleName === null) {
                $moduleName = $this->_defaultModule;
            }

            if (!isset($this->_modules[$moduleName])) {
                throw new Exception('Module does not exists: \'' . $moduleName . '\'');
            }

            $moduleObject = null;

            $this->fireEvent('application:beforeStartModule', $this, $moduleName);
            $moduleObject = $this->_dependencyInjector->getShared($this->_modules[$moduleName]);
            $moduleObject->registerAutoloaders($this->_dependencyInjector);
            $moduleObject->registerServices($this->_dependencyInjector);
            $this->fireEvent('application:afterStartModule', $this, $moduleObject);

            $dispatcher = $this->_dependencyInjector->getShared('dispatcher');
            if ($dispatcher->getDefaultNamespace() === null) {
                if ($moduleName === '') {
                    $dispatcher->setDefaultNamespace($this->_baseNamespace . '\\Controllers');
                } else {
                    $dispatcher->setDefaultNamespace($this->_baseNamespace . "\\$moduleName\\Controllers");
                }
            }

            $dispatcher->setModuleName($moduleName);
            $dispatcher->setNamespaceName($this->_baseNamespace . ($moduleName === '' ? '' : "\\$moduleName") . "\\Controllers");
            $dispatcher->setControllerName($router->getControllerName());
            $dispatcher->setActionName($router->getActionName());
            $dispatcher->setParams($router->getParams());

            $this->fireEvent('application:beforeHandleRequest', $this, $dispatcher);

            $controller = $dispatcher->dispatch();
            if ($controller === false) {
                return false;
            }

            $response = $this->_getResponse($dispatcher->getReturnedValue(), $moduleName,
              $dispatcher->getControllerName(), $dispatcher->getActionName());

            $this->fireEvent('application:afterHandleRequest', $this, $controller);

            $this->fireEvent('application:beforeSendResponse', $this, $response);

            $response->sendHeaders();
            $response->sendCookies();

            return $response;
        }


        /**
         * @param mixed $actionReturnValue
         * @param $moduleName
         * @param string $controller
         * @param string $action
         * @return \ManaPHP\Http\ResponseInterface
         * @throws \ManaPHP\Mvc\Application\Exception
         */
        protected function _getResponse($actionReturnValue, $moduleName, $controller, $action)
        {
            if ($actionReturnValue === false) {
                return $this->_dependencyInjector->getShared('response');
            } elseif ($actionReturnValue instanceof ResponseInterface) {
                return $actionReturnValue;
            } else {
                if ($actionReturnValue === null) {
                    $content = '';
                } elseif (is_string($actionReturnValue)) {
                    $content = $actionReturnValue;
                } else {
                    throw new Exception('the return value of Action is invalid: ' . $actionReturnValue);
                }

                $response = $this->_dependencyInjector->getShared('response');

                if ($this->_implicitView === true) {
                    $view = $this->_dependencyInjector->getShared('view');
                    if ($view->getViewsDir() === null) {
                        if ($moduleName === '') {
                            $view->setViewsDir($this->_baseDirectory . "/Views");
                        } else {
                            $view->setViewsDir($this->_baseDirectory . "/$moduleName/Views");
                        }
                    }

                    $view->start();
                    $view->setContent($content);
                    $view->renderView($controller, $action);
                    $view->finish();
                    $response->setContent($view->getContent());
                } else {
                    $response->setContent($content);
                }

                return $response;
            }
        }
    }
}
