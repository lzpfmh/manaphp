<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/1/18
 */

namespace ManaPHP{
    interface ComponentInterface{

        /**
         * Magic method __get
         *
         * @param string $propertyName
         * @return object
         */
        public function __get($propertyName);

        /**
         * Sets the dependency injector
         *
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function setDI($dependencyInjector);


        /**
         * Returns the internal dependency injector
         *
         * @return \ManaPHP\DiInterface
         */
        public function getDI();


        /**
         * Attach a listener to the events manager
         *
         * @param string $event
         * @param object|callable $handler
         * @throws \ManaPHP\Event\Exception
         */
        public function attachEvent($event, $handler);


        /**
         * Fires an event in the events manager causing that the active listeners will be notified about it
         *
         * @param string $event
         * @param object $source
         * @param mixed $data
         * @return mixed
         */
        public function fireEvent($event, $source, $data = null);
    }
}