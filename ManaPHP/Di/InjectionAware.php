<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/20
 * Time: 17:31
 */
namespace ManaPHP\Di {

    trait InjectionAware
    {
        /**
         * Dependency Injector
         *
         * @var \ManaPHP\DiInterface
         */
        protected $_dependencyInjector = null;

        /**
         * Sets the dependency injector
         *
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function setDI($dependencyInjector)
        {
            $this->_dependencyInjector = $dependencyInjector;
        }


        /**
         * Returns the internal dependency injector
         *
         * @return \ManaPHP\DiInterface
         */
        public function getDI()
        {
            return $this->_dependencyInjector;
        }
    }
}