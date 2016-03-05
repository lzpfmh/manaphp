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
         * @param false|string $layout
         * @return static
         */

        public function setLayout($layout='Default');


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
        public function renderView($controllerName, $actionName, $params = null);


        /**
         * Choose a view different to render than last-controller/last-action
         *
         * @param string $actionView
         * @param string $controllerView
         */
        public function pickView($actionView,$controllerView=null);


        /**
         * Renders a partial view
         *
         * @param string $partialPath
         * @param array $vars
         */
        public function renderPartial($partialPath, $vars=null);


        /**
         * Finishes the render process by stopping the output buffering
         */
        public function finish();


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
