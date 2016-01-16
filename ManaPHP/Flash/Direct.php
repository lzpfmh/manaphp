<?php

namespace ManaPHP\Flash {

    use ManaPHP\Flash;

    /**
     * ManaPHP\Flash\Direct
     *
     * This is a variant of the ManaPHP\Flash that immediately outputs any message passed to it
     */
    class Direct extends Flash
    {
        protected $_messages;

        /**
         * Outputs a message
         *
         * @param  string $type
         * @param  string $message
         * @return void
         */
        public function _message($type, $message)
        {
            $this->_messages[] = [$type, $message];
        }

        public function output($remove = true)
        {
            foreach ($this->_messages as $item) {
                list($type, $message) = $item;

                if (isset($this->_cssClasses[$type])) {
                    $cssClasses = $this->_cssClasses;
                } else {
                    $cssClasses = '';
                }

                $html = '<div class="' . $cssClasses . '">' . $message . '</div>' . PHP_EOL;
                $this->_messages[] = $html;
            }
        }
    }
}
