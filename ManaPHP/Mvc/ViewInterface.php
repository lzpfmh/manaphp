<?php

namespace ManaPHP\Mvc {

    /**
     * ManaPHP\Mvc\ViewInterface initializer
     */
    interface ViewInterface
    {

        /**
         * Sets views directory. Depending of your platform
         *
         * @param string $viewsDir
         * @return static
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
         *
         * @return static
         */

        public function setLayout($layout = 'Default');


        /**
         * Adds parameter to view
         *
         * @param string $name
         * @param mixed $value
         * @return static
         */
        public function setVar($name, $value);


        /**
         * Adds parameters to view
         *
         * @param $vars
         * @return static
         */
        public function setVars($vars);


        /**
         * Returns a parameter previously set in the view
         *
         * @param string $name
         * @return mixed
         */
        public function getVar($name);


        /**
         * @return array
         */
        public function getVars();
        
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
         * Starts rendering process enabling the output buffering
         *
         * @return static
         */
        public function start();


        /**
         * Register template engines
         *
         * @param array $engines
         * @return static
         */
        public function registerEngines($engines);


        /**
         * Executes render process from dispatching data
         *
         * @param string $controllerName
         * @param string $actionName
         */
        public function renderView($controllerName, $actionName);


        /**
         * Choose a view different to render than last-controller/last-action
         *
         * @param $view
         * @return static
         */
        public function pickView($view);


        /**
         * Renders a partial view
         *
         * @param string $partialPath
         * @param array $vars
         * @return static
         */
        public function renderPartial($partialPath, $vars = null);


        /**
         * Finishes the render process by stopping the output buffering
         *
         * @return static
         */
        public function finish();


        /**
         * Externally sets the view content
         *
         * @param string $content
         * @return static
         */
        public function setContent($content);


        /**
         * Returns cached output from another view stage
         *
         * @return string
         */
        public function getContent();
    }
}
