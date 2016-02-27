<?php

namespace ManaPHP\Mvc\View\Engine {

    use ManaPHP\Component;
    use ManaPHP\Mvc\View\EngineInterface;

    /**
     * ManaPHP\Mvc\View\Engine\Php
     *
     * Adapter to use PHP itself as template engine
     */
    class Php extends Component implements EngineInterface
    {
        /**
         * Php constructor.
         * @param \ManaPHP\Mvc\ViewInterface $view
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function __construct($dependencyInjector = null)
        {
            $this->_dependencyInjector = $dependencyInjector;
        }

        /**
         * Renders a view using the template engine
         *
         * @param string $file
         * @param array $vars
         */
        public function render($file, $vars = null)
        {
            if (is_array($vars)) {
                extract($vars);
            }

            /** @noinspection PhpIncludeInspection */
            require($file);
        }
    }
}
