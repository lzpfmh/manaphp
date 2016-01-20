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
        protected $_view;

        /**
         * Php constructor.
         * @param \ManaPHP\Mvc\ViewInterface $view
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function __construct($view, $dependencyInjector = null)
        {
            $this->_view = $view;
            $this->_dependencyInjector = $dependencyInjector;
        }

        /**
         * Renders a view using the template engine
         *
         * @param string $file
         * @param array $vars
         * @param bool $mustClean
         */
        public function render($file, $vars = null, $mustClean = false)
        {
            if ($mustClean) {
                ob_clean();
            }

            if (is_array($vars)) {
                extract($vars);
            }

            /** @noinspection PhpIncludeInspection */
            require($file);

            if ($mustClean) {
                $this->_view->setContent(ob_get_contents());
            }
        }
    }
}
