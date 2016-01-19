<?php

namespace ManaPHP\Mvc {

    use ManaPHP\Component;

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

        const LEVEL_MAIN_LAYOUT = 5;


        const LEVEL_LAYOUT = 3;


        const LEVEL_ACTION_VIEW = 1;

        const LEVEL_NO_RENDER = 0;

        protected $_options;

        protected $_basePath;

        protected $_content;

        protected $_renderLevel;

        protected $_currentRenderLevel;

        protected $_disabledLevels;

        /**
         * @var array
         */
        protected $_viewParams=[];

        protected $_layout;

        protected $_layoutsDir='Layouts';

        protected $_partialsDir;

        protected $_viewsDir;

        protected $_templatesBefore;

        protected $_templatesAfter;

        protected $_engines;

        protected $_registeredEngines;

        protected $_mainView;

        protected $_controllerName;

        protected $_actionName;

        protected $_params;

        protected $_pickView;

        protected $_cache;

        protected $_cacheLevel;

        protected $_activeRenderPath;

        protected $_disabled;

        /**
         * \ManaPHP\Mvc\View constructor
         *
         * @param array $options
         */
        public function __construct($options = null)
        {
            $this->_options=$options;
        }


        /**
         * Sets views directory. Depending of your platform, always add a trailing slash or backslash
         *
         * @param string $viewsDir
         * @return static
         */
        public function setViewsDir($viewsDir)
        {
            $this->_viewsDir=ltrim('\\/',$viewsDir).'/';

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
         * Sets base path. Depending of your platform, always add a trailing slash or backslash
         *
         * <code>
         *    $view->setBasePath(__DIR__ . '/');
         * </code>
         *
         * @param string $basePath
         * @return \ManaPHP\Mvc\View
         */
        public function setBasePath($basePath)
        {
        }


        /**
         * Returns the render level for the view
         *
         * @return int
         */
        public function getCurrentRenderLevel()
        {
        }


        /**
         * Returns the render level for the view
         *
         * @return int
         */
        public function getRenderLevel()
        {
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
         * @return \ManaPHP\Mvc\View
         */
        public function setRenderLevel($level)
        {
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
            $this->_disabledLevels=$level;
            return $this;
        }


        /**
         * Returns an array with disabled render levels
         *
         * @return array
         */
        public function getDisabledLevels()
        {
        }


        /**
         * Change the layout to be used instead of using the name of the latest controller name
         *
         * <code>
         *    $this->view->setLayout('main');
         * </code>
         *
         * @param string $layout
         * @return \ManaPHP\Mvc\View
         */
        public function setLayout($layout)
        {
            $this->_layout=$layout;
            return $this;
        }


        /**
         * Returns the name of the main view
         *
         * @return string
         */
        public function getLayout()
        {
            return $this->_layout;
        }

        /**
         * Set all the render params
         *
         *<code>
         *    $this->view->setVars(array('products' => $products));
         *</code>
         *
         * @param array $params
         * @param boolean $merge
         * @return static
         */
        public function setVars($params, $merge = true)
        {
            if($merge){
                $this->_viewParams=array_merge($this->_viewParams,$params);
            }else{
                $this->_viewParams=$params;
            }

            return $this;
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
            $this->_viewParams[$key]=$value;
        }


        /**
         * Returns a parameter previously set in the view
         *
         * @param string $key
         * @return mixed
         */
        public function getVar($key)
        {
            if(isset($this->_viewParams[$key])){
                return $this->_viewParams[$key];
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
            $this->_content=null;
            return $this;
        }


        /**
         * Loads registered template engines, if none is registered it will use \ManaPHP\Mvc\View\Engine\Php
         *
         * @return array
         */
        protected function _loadTemplateEngines()
        {
        }


        /**
         * Checks whether view exists on registered extensions and render it
         *
         * @param array $engines
         * @param string $viewPath
         * @param boolean $silence
         * @param boolean $mustClean
         * @param \ManaPHP\Cache\BackendInterface $cache
         */
        protected function _engineRender()
        {
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
            $this->_registeredEngines=$engines;
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
        public function render($controllerName, $actionName, $params = null)
        {
            $this->_controllerName=$controllerName;
            $this->_actionName=$actionName;
            $this->_params=$params;

            $this->_currentRenderLevel=0;

            /**
             * If the view is disabled we simply update the buffer from any output produced in the controller
             */
            if($this->_disabled){
                $this->_content=ob_get_contents();
                return false;
            }



            if($this->_pickView ===null){
                $renderView=$controllerName.'/'.$actionName;
                $layoutName=$this->_layoutsDir;
            }else{

            }


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
         * @param string|array $renderView
         * @return \ManaPHP\Mvc\View
         */
        public function pick($renderView)
        {
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
         * @param array $params
         */
        public function partial($partialPath)
        {
        }


        /**
         * Perform the automatic rendering returning the output as a string
         *
         * <code>
         *    $template = $this->view->getRender('products', 'show', array('products' => $products));
         * </code>
         *
         * @param string $controllerName
         * @param string $actionName
         * @param array $params
         * @param mixed $configCallback
         * @return string
         */
        public function getRender($controllerName, $actionName, $params = null, $configCallback = null)
        {
        }


        /**
         * Finishes the render process by stopping the output buffering
         *
         * @return \ManaPHP\Mvc\View
         */
        public function finish()
        {
        }


        /**
         * Create a \ManaPHP\Cache based on the internal cache options
         *
         * @return \ManaPHP\Cache\BackendInterface
         */
        protected function _createCache()
        {
        }


        /**
         * Check if the component is currently caching the output content
         *
         * @return boolean
         */
        public function isCaching()
        {
        }


        /**
         * Returns the cache instance used to cache
         *
         * @return \ManaPHP\Cache\BackendInterface
         */
        public function getCache()
        {
        }


        /**
         * Cache the actual view render to certain level
         *
         *<code>
         *  $this->view->cache(array('key' => 'my-key', 'lifetime' => 86400));
         *</code>
         *
         * @param boolean|array $options
         * @return \ManaPHP\Mvc\View
         */
        public function cache($options = null)
        {
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
            $this->_content=$content;
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
         * Returns the path of the view that is currently rendered
         *
         * @return string
         */
        public function getActiveRenderPath()
        {
        }


        /**
         * Disables the auto-rendering process
         *
         * @return \ManaPHP\Mvc\View
         */
        public function disable()
        {
            $this->_disabled=true;
        }


        /**
         * Enables the auto-rendering process
         *
         * @return \ManaPHP\Mvc\View
         */
        public function enable()
        {
            $this->_disabled=false;
        }


        /**
         * Whether automatic rendering is enabled
         *
         * @return bool
         */
        public function isDisabled()
        {
            return
        }


        /**
         * Magic method to pass variables to the views
         *
         *<code>
         *    $this->view->products = $products;
         *</code>
         *
         * @param string $key
         * @param mixed $value
         *
         */
        public function __set($key, $value)
        {
            $this->_viewParams[$key]=$value;
        }


        /**
         * Magic method to retrieve a variable passed to the view
         *
         *<code>
         *    echo $this->view->products;
         *</code>
         *
         * @param string $key
         * @return mixed
         */
        public function __get($key)
        {
            if(isset($this->_viewParams[$key])){
                return $this->_viewParams[$key];
            }else{
                return null;
            }
        }


        /**
         * Magic method to inaccessible a variable passed to the view
         *
         *<code>
         *    isset($this->view->products)
         *</code>
         *
         * @param string $key
         * @return bool
         */
        public function __isset($key)
        {
            return isset($this->_viewParams[$key]);
        }

    }
}
