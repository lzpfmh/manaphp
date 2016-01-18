<?php

namespace ManaPHP\Events {

    /**
     * ManaPHP\Event\EventsAwareInterface initializer
     */
    interface EventsAwareInterface
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
