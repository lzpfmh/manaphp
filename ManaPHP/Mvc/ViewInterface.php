<?php

namespace ManaPHP\Mvc {

    /**
     * ManaPHP\Mvc\ViewInterface initializer
     */
    interface ViewInterface
    {

        /**
         * Sets views directory. Depending of your platform, always add a trailing slash or backslash
         *
         * @param string $viewsDir
         */
        public function setViewsDir($viewsDir);


        /**
         * Gets views directory
         *
         * @return string
         */
        public function getViewsDir();


        /**
         * Sets base path. Depending of your platform, always add a trailing slash or backslash
         *
         * @param string $basePath
         */
        public function setBasePath($basePath);


        /**
         * Gets the current render level
         *
         * @return string
         */
        public function getCurrentRenderLevel();


        /**
         * Gets the render level for the view
         *
         * @return string
         */
        public function getRenderLevel();


        /**
         * Sets the render level for the view
         *
         * @param string $level
         */
        public function setRenderLevel($level);


        /**
         * Change the layout to be used instead of using the name of the latest controller name
         *
         * @param string $layout
         */
        public function setLayout($layout);


        /**
         * Returns the name of the main view
         *
         * @return string
         */
        public function getLayout();


        /**
         * Adds parameters to views
         *
         * @param string $key
         * @param mixed $value
         */
        public function setVar($key, $value);


        /**
         * Gets the name of the controller rendered
         *
         * @return string
         */
        public function getControllerName();


        /**
         * Gets the name of the action rendered
         *
         * @return string
         */
        public function getActionName();


        /**
         * Gets extra parameters of the action rendered
         *
         * @return array
         */
        public function getParams();


        /**
         * Starts rendering process enabling the output buffering
         */
        public function start();


        /**
         * Register template engines
         *
         * @param array $engines
         */
        public function registerEngines($engines);


        /**
         * Executes render process from dispatching data
         *
         * @param string $controllerName
         * @param string $actionName
         * @param array $params
         */
        public function render($controllerName, $actionName, $params = null);


        /**
         * Choose a view different to render than last-controller/last-action
         *
         * @param string $renderView
         */
        public function pick($renderView);


        /**
         * Renders a partial view
         *
         * @param string $partialPath
         * @return string
         */
        public function partial($partialPath);


        /**
         * Finishes the render process by stopping the output buffering
         */
        public function finish();


        /**
         * Returns the cache instance used to cache
         *
         * @return \ManaPHP\Cache\BackendInterface
         */
        public function getCache();


        /**
         * Cache the actual view render to certain level
         *
         * @param boolean|array $options
         */
        public function cache($options = null);


        /**
         * Externally sets the view content
         *
         * @param string $content
         */
        public function setContent($content);


        /**
         * Returns cached output from another view stage
         *
         * @return string
         */
        public function getContent();


        /**
         * Returns the path of the view that is currently rendered
         *
         * @return string
         */
        public function getActiveRenderPath();


        /**
         * Disables the auto-rendering process
         *
         */
        public function disable();


        /**
         * Enables the auto-rendering process
         *
         */
        public function enable();


        /**
         * Whether the automatic rendering is disabled
         *
         * @return bool
         */
        public function isDisabled();

    }
}
