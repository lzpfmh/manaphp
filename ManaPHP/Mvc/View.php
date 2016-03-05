<?php

namespace ManaPHP\Mvc {

    use ManaPHP\Component;
    use ManaPHP\Mvc\View\Engine\Php;
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
        const LEVEL_ACTION= 1;
        const LEVEL_CONTROLLER = 2;
        const LEVEL_MAIN = 4;

        /**
         * @var array
         */
        protected $_options;

        /**
         * @var string
         */
        protected $_content;

        /**
         * @var int
         */
        protected $_renderLevel=self::LEVEL_MAIN;

        /**
         * @var int
         */
        protected $_disabledLevel;

        /**
         * @var array
         */
        protected $_viewVars = [];

        /**
         * @var string
         */
        protected $_controllerView;

        /**
         * @var string
         */
        protected $_layoutsDir = 'Layouts';

        /**
         * @var string
         */
        protected $_viewsDir;

        /**
         * @var array
         */
        protected $_resolvedEngines = [];

        /**
         * @var array
         */
        protected $_registeredEngines = [];

        /**
         * @var string
         */
        protected $_mainView='Main';

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
        protected $_params;

        /**
         * @var string
         */
        protected $_pickView;

        /**
         * @var bool
         */
        protected $_disabled;

        /**
         * \ManaPHP\Mvc\View constructor
         *
         * @param array $options
         */
        public function __construct($options = null)
        {
            $this->_options = $options;
        }


        /**
         * Sets views directory. Depending of your platform, always add a trailing slash or backslash
         *
         * @param string $viewsDir
         * @return static
         */
        public function setViewsDir($viewsDir)
        {
            $this->_viewsDir = rtrim($viewsDir,'\\/');

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
         * Returns the render level for the view
         *
         * @return int
         */
        public function getRenderLevel()
        {
            return $this->_renderLevel;
        }


        /**
         * Sets the render level for the view
         *
         * <code>
         *    //Render the view related to the controller only
         *    $this->view->setRenderLevel(View::LEVEL_LAYOUT);
         * </code>
         *
         * @param string $level
         * @return static
         */
        public function setRenderLevel($level)
        {
            $this->_renderLevel=$level;

            return $this;
        }


        /**
         * Disables a specific level of rendering
         *
         *<code>
         * //Render all levels except ACTION level
         * $this->view->disableLevel(View::LEVEL_ACTION_VIEW);
         *</code>
         *
         * @param int $level
         * @return static
         */
        public function disableLevel($level)
        {
            $this->_disabledLevel |= $level;
            return $this;
        }


        /**
         * Returns an array with disabled render levels
         *
         * @return int
         */
        public function getDisabledLevel()
        {
            return $this->_disabledLevel;
        }


        /**
         * Returns the name of the main view
         *
         * @return string
         */
        public function getControllerView()
        {
            return $this->_controllerView;
        }


        /**
         * Set a single view parameter
         *
         *<code>
         *    $this->view->setVar('products', $products);
         *</code>
         *
         * @param string $key
         * @param mixed $value
         * @return \ManaPHP\Mvc\View
         */
        public function setVar($key, $value)
        {
            $this->_viewVars[$key] = $value;
        }


        /**
         * Returns a parameter previously set in the view
         *
         * @param string $key
         * @return mixed
         */
        public function getVar($key)
        {
            if (isset($this->_viewVars[$key])) {
                return $this->_viewVars[$key];
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
         * Gets extra parameters of the action rendered
         *
         * @return array
         */
        public function getParams()
        {
            return $this->_params;
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
                throw new Exception("Invalid template engine registration for extension: " . $extension);
            }

            if (!($engine instanceof EngineInterface)) {
                throw new Exception('Invalid template engine: it is not implements \ManaPHP\Mvc\ViewInterface');
            }

            return $engine;
        }

        /**
         * Checks whether view exists on registered extensions and render it
         *
         * @param string $relativePath
         * @param boolean $mustClean
         * @throws \ManaPHP\Mvc\View\Exception
         */
        protected function _engineRender($relativePath, $mustClean)
        {
            $notExists = true;

            $fileWithoutExtension =$relativePath;

            if (count($this->_registeredEngines) === 0) {
                $this->_registeredEngines['.phtml'] = new Php($this->_dependencyInjector);
            }

            foreach ($this->_registeredEngines as $extension => $engine) {
                $file = $fileWithoutExtension . $extension;
                if (file_exists($file)) {
                    if (isset($this->_resolvedEngines[$extension])) {
                        $engine = $this->_resolvedEngines[$extension];
                    } else {
                        $engine = $this->_loadEngine($extension);
                        $this->_resolvedEngines[$extension] = $engine;
                    }

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
         * @return \ManaPHP\Mvc\View
         */
        public function registerEngines($engines)
        {
            $this->_resolvedEngines = [];
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


        public function exists($view)
        {
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
         * @param array $params
         * @return static
         */
        public function renderAction($controllerName, $actionName, $params = null)
        {
            $this->_controllerName = $controllerName;
            $this->_actionName = $actionName;
            $this->_params = $params;

            /**
             * If the view is disabled we simply update the buffer from any output produced in the controller
             */
            $this->_content = ob_get_contents();

            if ($this->_disabled) {
                return false;
            }

            /**
             * Check if the user has picked a view diferent than the automatic
             */
            if ($this->_pickView === null) {
                $actionView = $controllerName . '/' . $actionName;
            } else {
                $actionView = $this->_pickView;
            }
            $actionViewPath=$this->_viewsDir.'/'.$actionView;

            if ($this->_controllerView === null) {
                $controllerView = $controllerName;
            } else {
                $controllerView = $this->_controllerView;
            }
            $controllerViewPath=$this->_viewsDir.'/'.$this->_layoutsDir.'/'.$controllerView;

            $mainViewPath=$this->_viewsDir.'/'.$this->_mainView;
            $this->fireEvent('view:beforeRender', $this);

            $mustClean = true;

            /**
             * render action view
             */
            if ($this->_renderLevel >= self::LEVEL_ACTION) {
                if (!($this->_disabledLevel & self::LEVEL_ACTION)) {
                    $this->_engineRender($actionViewPath, $mustClean);
                }
            }

            /**
             * render controller layout
             */
            if ($this->_renderLevel >= self::LEVEL_CONTROLLER) {
                if (!($this->_disabledLevel & self::LEVEL_CONTROLLER)) {
                    $this->_engineRender($controllerViewPath, $mustClean);
                }
            }

            /**
             * render main view
             */
            if ($this->_renderLevel >= self::LEVEL_MAIN) {
                if (!($this->_disabledLevel & self::LEVEL_MAIN)) {
                    $this->_engineRender($mainViewPath, $mustClean);
                }
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
         * @param string $actionView
         * @param string $controllerView
         * @return static
         */
        public function pickView($actionView,$controllerView=null)
        {
            $this->_pickView=$actionView;
            if($controllerView ===null){
                $this->_controllerView=explode('/',$actionView)[0];
            }else{
                $this->_controllerView=$controllerView;
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
         * @throws \ManaPHP\Mvc\View\Exception
         */
        public function renderPartial($partialPath, $vars = null)
        {
            $viewVars = $this->_viewVars;

            if (is_array($vars)) {
                $this->_viewVars = array_merge($this->_viewVars, $vars);
            }

            $this->_engineRender($this->_viewsDir . '/' . $partialPath, false);

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


        /**
         * Disables the auto-rendering process
         *
         * @return \ManaPHP\Mvc\View
         */
        public function disable()
        {
            $this->_disabled = true;
        }


        /**
         * Enables the auto-rendering process
         *
         * @return \ManaPHP\Mvc\View
         */
        public function enable()
        {
            $this->_disabled = false;
        }


        /**
         * Whether automatic rendering is enabled
         *
         * @return bool
         */
        public function isDisabled()
        {
            return $this->_disabled;
        }
    }
}
