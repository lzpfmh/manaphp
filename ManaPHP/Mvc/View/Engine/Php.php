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
        public function __construct($view=null,$dependencyInjector = null)
        {
            $this->_view=$view;
            $this->_dependencyInjector = $dependencyInjector;
        }

        /**
         * Renders a view using the template engine
         *
         * @param string $file
         * @param array $vars
         * @throws \ManaPHP\Mvc\View\Engine\Exception
         */
        public function render($file, $vars = null)
        {
            if(isset($vars['view'])){
                throw new Exception('variable \'view\' is reserved for PHP view engine.');
            }

            $view=$this->_view;

            true||$view;

            if (is_array($vars)) {
                extract($vars, EXTR_SKIP);
            }

            /** @noinspection PhpIncludeInspection */
            require($file);
        }
    }
}
