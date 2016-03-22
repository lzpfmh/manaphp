<?php

namespace ManaPHP\Mvc {

    use ManaPHP\Component;
    use ManaPHP\Mvc\View\EngineInterface;
    use ManaPHP\Mvc\View\Exception;

    /**
     * ManaPHP\Mvc\View
     *
     * ManaPHP\Mvc\View is a class for working with the "view" portion of the model-view-controller pattern.
     * That is, it exists to help keep the view script separate from the model and controller scripts.
     * It provides a system of helpers, output filters, and variable escaping.
     *
     * <code>
     * //Setting views directory
     * $view = new ManaPHP\Mvc\View();
     * $view->setViewsDir('app/views/');
     *
     * $view->start();
     * //Shows recent posts view (app/views/posts/recent.phtml)
     * $view->render('posts', 'recent');
     * $view->finish();
     *
     * //Printing views output
     * echo $view->getContent();
     * </code>
     */
    class View extends Component implements ViewInterface
    {
        /**
         * @var string
         */
        protected $_content;

        /**
         * @var array
         */
        protected $_viewVars = [];

        /**
         * @var string
         */
        protected $_viewsDir;

        /**
         * @var string
         */
        protected $_layoutsDir = 'Layouts';

        /**
         * @var false|string|null
         */
        protected $_layout = null;

        /**
         * @var \ManaPHP\Mvc\View\EngineInterface[]
         */
        protected $_resolvedEngines = [];

        /**
         * @var array
         */
        protected $_registeredEngines = [];


        /**
         * @var string
         */
        protected $_controllerName;

        /**
         * @var string
         */
        protected $_actionName;


        public function __construct()
        {
            $this->_registeredEngines['.phtml'] = 'ManaPHP\Mvc\View\Engine\Php';
        }

        /**
         * Sets views directory. Depending of your platform
         *
         * @param string $viewsDir
         * @return static
         */
        public function setViewsDir($viewsDir)
        {
            $this->_viewsDir = str_replace('\\', '/', rtrim($viewsDir, '\\/'));

            return $this;
        }


        /**
         * Gets views directory
         *
         * @return string
         */
        public function getViewsDir()
        {
            return $this->_viewsDir;
        }


        /**
         * @param false|string $layout
         * @return static
         */
        public function setLayout($layout = 'Default')
        {
            $this->_layout = $layout;

            return $this;
        }


        /**
         * Set a single view parameter
         *
         *<code>
         *    $this->view->setVar('products', $products);
         *</code>
         *
         * @param string $name
         * @param mixed $value
         * @return static
         */
        public function setVar($name, $value)
        {
            $this->_viewVars[$name] = $value;

            return $this;
        }


        /**
         * Adds parameters to view
         *
         * @param $vars
         * @return static
         */
        public function setVars($vars){
            $this->_viewVars=array_merge($this->_viewVars,$vars);

            return $this;
        }


        /**
         * Returns a parameter previously set in the view
         *
         * @param string $name
         * @return mixed
         */
        public function getVar($name)
        {
            if (isset($this->_viewVars[$name])) {
                return $this->_viewVars[$name];
            }

            return null;
        }


        /**
         * Gets the name of the controller rendered
         *
         * @return string
         */
        public function getControllerName()
        {
            return $this->_controllerName;
        }


        /**
         * Gets the name of the action rendered
         *
         * @return string
         */
        public function getActionName()
        {
            return $this->_actionName;
        }


        /**
         * Starts rendering process enabling the output buffering
         *
         * @return static
         */
        public function start()
        {
            ob_start();
            $this->_content = null;

            return $this;
        }


        /**
         * @param string $extension
         * @return \ManaPHP\Mvc\View\EngineInterface
         * @throws \ManaPHP\Mvc\View\Exception
         */
        protected function _loadEngine($extension)
        {
            $arguments = [$this, $this->_dependencyInjector];
            $engineService = $this->_registeredEngines[$extension];
            if ($engineService instanceof \Closure) {
                $engine = call_user_func_array($engineService, $arguments);
            } elseif (is_object($engineService)) {
                $engine = $engineService;
            } elseif (is_string($engineService)) {
                $engine = $this->_dependencyInjector->getShared($engineService, $arguments);
            } else {
                throw new Exception('Invalid template engine registration for extension: ' . $extension);
            }

            if (!($engine instanceof EngineInterface)) {
                throw new Exception('Invalid template engine: it is not implements \ManaPHP\Mvc\ViewInterface');
            }

            return $engine;
        }

        /**
         * Checks whether view exists on registered extensions and render it
         *
         * @param string $viewPath
         * @param string $relativePath
         * @param boolean $mustClean
         * @throws \ManaPHP\Mvc\View\Exception
         */
        protected function _engineRender($viewPath, $relativePath, $mustClean)
        {
            $notExists = true;

            $fileWithoutExtension = $viewPath . '/' . $relativePath;

            foreach ($this->_registeredEngines as $extension => $engine) {
                $file = $fileWithoutExtension . $extension;
                if (file_exists($file)) {
                    if (DIRECTORY_SEPARATOR === '\\') {
                        $realPath = str_replace('\\', '/', realpath($file));
                        if ($file !== $realPath) {
                            trigger_error("File name ($realPath) case mismatch for $file", E_USER_ERROR);
                        }
                    }

                    if (!isset($this->_resolvedEngines[$extension])) {
                        $this->_resolvedEngines[$extension] = $this->_loadEngine($extension);
                    }

                    $engine = $this->_resolvedEngines[$extension];

                    $this->fireEvent('view:beforeRenderView', $this, $file);
                    if ($mustClean) {
                        ob_clean();
                    }
                    $engine->render($file, $this->_viewVars);
                    if ($mustClean) {
                        $this->setContent(ob_get_contents());
                    }
                    $notExists = false;
                    $this->fireEvent('view:afterRenderView', $this, $file);
                    break;
                }
            }

            if ($notExists) {
                throw new Exception("View '$fileWithoutExtension' was not found in the views directory");
            }
        }


        /**
         * Register template engines
         *
         *<code>
         *$this->view->registerEngines(array(
         *  ".phtml" => "ManaPHP\Mvc\View\Engine\Php",
         *  ".volt" => "ManaPHP\Mvc\View\Engine\Volt",
         *  ".mhtml" => "MyCustomEngine"
         *));
         *</code>
         *
         * @param array $engines
         * @return static
         */
        public function registerEngines($engines)
        {
            $this->_registeredEngines = $engines;

            return $this;
        }


        /**
         * Returns the registered template engines
         *
         * @brief array \ManaPHP\Mvc\View::getRegisteredEngines()
         */
        public function getRegisteredEngines()
        {
            return $this->_registeredEngines;
        }


        /**
         * Executes render process from dispatching data
         *
         *<code>
         * //Shows recent posts view (app/views/posts/recent.phtml)
         * $view->start()->render('posts', 'recent')->finish();
         *</code>
         *
         * @param string $controllerName
         * @param string $actionName
         * @return static
         * @throws \ManaPHP\Mvc\View\Exception
         */
        public function renderView($controllerName, $actionName)
        {
            if ($this->_controllerName === null) {
                $this->_controllerName = $controllerName;
            }

            if ($this->_actionName === null) {
                $this->_actionName = $actionName;
            }

            $this->fireEvent('view:beforeRender', $this);

            $mustClean = true;

            $view = $this->_controllerName . '/' . ucfirst($this->_actionName);
            $this->_engineRender($this->_viewsDir, $view, $mustClean);

            if ($this->_layout !== false) {
                if (is_string($this->_layout)) {
                    $layout = $this->_layout;
                } else {
                    $layout = $this->_controllerName;
                }

                $this->_engineRender($this->_viewsDir, $this->_layoutsDir . '/' . ucfirst($layout), $mustClean);
            }

            $this->fireEvent('view:afterRender', $this);

            return $this;
        }


        /**
         * Choose a different view to render instead of last-controller/last-action
         *
         * <code>
         * class ProductsController extends \ManaPHP\Mvc\Controller
         * {
         *
         *    public function saveAction()
         *    {
         *
         *         //Do some save stuff...
         *
         *         //Then show the list view
         *         $this->view->pick("products/list");
         *    }
         * }
         * </code>
         *
         * @param string $view
         * @return static
         */
        public function pickView($view)
        {
            $parts = explode('/', $view);
            if (count($parts) === 1) {
                $this->_controllerName = null;
                $this->_actionName = $parts[0];
            } else {
                list($this->_controllerName, $this->_actionName) = $parts;
            }

            return $this;
        }


        /**
         * Renders a partial view
         *
         * <code>
         *    //Show a partial inside another view
         *    $this->partial('shared/footer');
         * </code>
         *
         * <code>
         *    //Show a partial inside another view with parameters
         *    $this->partial('shared/footer', array('content' => $html));
         * </code>
         *
         * @param string $partialPath
         * @param array $vars
         * @return static
         * @throws \ManaPHP\Mvc\View\Exception
         */
        public function renderPartial($partialPath, $vars = null)
        {
            if (strpos($partialPath, '/') === false) {
                $partialPath = $this->_controllerName . '/' . $partialPath;
            }

            $viewVars = $this->_viewVars;

            if (is_array($vars)) {
                $this->_viewVars = array_merge($this->_viewVars, $vars);
            }

            $this->_engineRender($this->_viewsDir, $partialPath, false);

            $this->_viewVars = $viewVars;
        }


        /**
         * Finishes the render process by stopping the output buffering
         *
         * @return static
         */
        public function finish()
        {
            ob_end_clean();

            return $this;
        }


        /**
         * Externally sets the view content
         *
         *<code>
         *    $this->view->setContent("<h1>hello</h1>");
         *</code>
         *
         * @param string $content
         * @return static
         */
        public function setContent($content)
        {
            $this->_content = $content;

            return $this;
        }


        /**
         * Returns cached output from another view stage
         *
         * @return string
         */
        public function getContent()
        {
            return $this->_content;
        }
    }
}
