<?php

namespace ManaPHP\Event {

    /**
     * ManaPHP\Event\EventAwareInterface initializer
     */
    interface EventAwareInterface
    {

        /**
         * Attach a listener to the events manager
         *
         * @param string $event
         * @param object|callable $handler
         */
        public function attachEvent($event, $handler);


        /**
         * Fires an event in the events manager causing that the active listeners will be notified about it
         *
         * @param string $event
         * @param object $source
         * @param mixed $data
         */
        public function fireEvent($event, $source, $data = null);
    }
}
