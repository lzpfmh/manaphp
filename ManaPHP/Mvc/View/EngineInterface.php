<?php

namespace ManaPHP\Mvc\View {

    /**
     * ManaPHP\Mvc\View\EngineInterface initializer
     */
    interface EngineInterface
    {

        /**
         * Php constructor.
         * @param \ManaPHP\Mvc\ViewInterface $view
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function __construct($view, $dependencyInjector = null);

        /**
         * Renders a view using the template engine
         *
         * @param string $file
         * @param array $vars
         * @param bool $mustClean
         */
        public function render($file, $vars = null, $mustClean = false);
    }
}
