<?php

namespace ManaPHP\Mvc {

    use ManaPHP\Component;
    use ManaPHP\Di\FactoryDefault;
    use ManaPHP\Http\ResponseInterface;
    use ManaPHP\Mvc\Application\Exception;
    use ManaPHP\Mvc\Application\NotFoundModuleException;
    use ManaPHP\Mvc\Dispatcher\NotFoundActionException;
    use ManaPHP\Mvc\Dispatcher\NotFoundControllerException;
    use ManaPHP\Mvc\Router\NotFoundRouteException;

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
     *            $this->registerModules(['frontend' ,'backend']);
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
        protected $_rootDirectory;

        /**
         * var string
         */
        protected $_rootNamespace;

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
         * @param string               $rootDirectory
         * @param string               $rootNamespace
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function __construct($rootDirectory, $rootNamespace = null, $dependencyInjector = null)
        {
            $this->_dependencyInjector = $dependencyInjector ?: new FactoryDefault();
            $this->_dependencyInjector->setShared('application', $this);

            $rootDirectory = str_replace('\\', '/', rtrim($rootDirectory, '\\/'));
            $rootNamespace = $rootNamespace ?: ucfirst(basename($rootDirectory));

            $this->_rootDirectory = $rootDirectory;
            $this->_rootNamespace = $rootNamespace;

            $this->loader->registerNamespaces([$rootNamespace => $rootDirectory])->register();
        }


        /**
         * By default. The view is implicitly buffering all the output
         * You can full disable the view component using this method
         *
         * @param boolean $implicitView
         *
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
         *        'frontend','backend'));
         *</code>
         *
         * @param array $modules
         *
         * @return static
         * @throws \ManaPHP\Mvc\Application\Exception
         */
        public function registerModules($modules)
        {
            //region DEBUG
            assert(is_array($modules));
            //endregion

            foreach ($modules as $module) {
                $moduleName = ucfirst($module);

                $this->_modules[$moduleName] = $this->_rootNamespace . "\\$moduleName\\Module";

                if ($this->_defaultModule === null) {
                    $this->_defaultModule = $moduleName;
                }
            }

            return $this;
        }


        /**
         * Handles a MVC request
         *
         * @param string                                            $uri
         * @param \ManaPHP\Mvc\Application\NotFoundHandlerInterface $notFoundHandler
         *
         * @return \ManaPHP\Http\ResponseInterface|boolean
         * @throws \ManaPHP\Mvc\Application\Exception|\ManaPHP\Event\Exception|\ManaPHP\Di\Exception|\ManaPHP\Mvc\Application\NotFoundModuleException|\ManaPHP\Mvc\Dispatcher\Exception
         */
        public function handle($uri = null, $notFoundHandler = null)
        {
            if ($this->_modules === null) {
                throw new Exception('modules is empty. please register it first.');
            }

            if ($this->fireEvent('application:boot') === false) {
                return false;
            }

            $router = $this->_dependencyInjector->getShared('router');

            if ($notFoundHandler === null) {
                $router->handle($uri, null, false);
            } else {
                try {
                    $router->handle($uri, null, false);
                } catch (NotFoundRouteException $e) {
                    return $notFoundHandler->notFoundRoute($e);
                }
            }

            $moduleName = $router->getModuleName();
            $controllerName = $router->getControllerName();
            $actionName = $router->getActionName();
            $params = $router->getParams();

            if ($moduleName === null) {
                $moduleName = $this->_defaultModule;
            }

            if (!isset($this->_modules[$moduleName])) {
                $notFoundModuleException = new NotFoundModuleException('Module does not exists: \'' . $moduleName . '\'');

                if ($notFoundHandler === null) {
                    throw $notFoundModuleException;
                } else {
                    return $notFoundHandler->notFoundModule($notFoundModuleException);
                }
            }

            $moduleObject = null;

            $this->fireEvent('application:beforeStartModule', $moduleName);
            $moduleObject = $this->_dependencyInjector->getShared($this->_modules[$moduleName]);
            $moduleObject->registerAutoloaders($this->_dependencyInjector);
            $moduleObject->registerServices($this->_dependencyInjector);
            $this->fireEvent('application:afterStartModule', $moduleObject);

            $dispatcher = $this->_dependencyInjector->getShared('dispatcher');
            if ($dispatcher->getRootNamespace() === null) {
                $dispatcher->setRootNamespace($this->_rootNamespace);
            }
            if ($this->_dependencyInjector->has('authorization')) {
                $dispatcher->attachEvent('dispatcher:beforeDispatch', function () use ($dispatcher) {
                    $dispatcher->getDependencyInjector()->getShared('authorization')->authorize($dispatcher);
                });
            }

            if ($notFoundHandler === null) {
                $controller = $dispatcher->dispatch($moduleName, $controllerName, $actionName, $params);
            } else {
                try {
                    $controller = $dispatcher->dispatch($moduleName, $controllerName, $actionName, $params);
                } catch (NotFoundControllerException $e) {
                    return $notFoundHandler->notFoundController($e);
                } catch (NotFoundActionException $e) {
                    return $notFoundHandler->notFoundAction($e);
                }
            }

            if ($controller === false) {
                return false;
            }

            $response = $this->_getResponse($dispatcher->getReturnedValue(), $moduleName,
                $dispatcher->getControllerName(), $dispatcher->getActionName());

            $this->fireEvent('application:beforeSendResponse', $response);

            $response->sendHeaders();
            $response->sendCookies();

            return $response;
        }


        /**
         * @param mixed  $actionReturnValue
         * @param        $module
         * @param string $controller
         * @param string $action
         *
         * @return \ManaPHP\Http\ResponseInterface
         * @throws \ManaPHP\Mvc\Application\Exception|\ManaPHP\Di\Exception
         */
        protected function _getResponse($actionReturnValue, $module, $controller, $action)
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
                    $view->setAppDir($this->_rootDirectory);

                    $view->setContent($content);
                    $view->renderView($module, $controller, $action);
                    $response->setContent($view->getContent());
                } else {
                    $response->setContent($content);
                }

                return $response;
            }
        }
    }
}
