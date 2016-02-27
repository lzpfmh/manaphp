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
        /**
         * Outputs a message
         *
         * @param  string $type
         * @param  string $message
         * @return void
         */
        public function _message($type, $message)
        {
            if (isset($this->_cssClasses[$type])) {
                $cssClasses = $this->_cssClasses[$type];
            } else {
                $cssClasses = '';
            }
            echo '<div class="' . $cssClasses . '">' . $message . '</div>' . PHP_EOL;
        }
    }
}
