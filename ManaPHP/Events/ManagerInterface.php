<?php

namespace ManaPHP\Events {

    /**
     * ManaPHP\Events\Manager
     *
     * ManaPHP Events Manager, offers an easy way to intercept and manipulate, if needed,
     * the normal flow of operation. With the EventsManager the developer can create hooks or
     * plugins that will offer monitoring of data, manipulation, conditional execution and much more.
     */
    interface ManagerInterface
    {

        /**
         * Attach a listener to the events manager
         *
         * @param string $event
         * @param object|callable $handler
         */
        public function attach($event, $handler);


        /**
         * Fires an event in the events manager causing that the active listeners will be notified about it
         *
         * @param string $event
         * @param object $source
         * @param mixed $data
         */
        public function fire($event, $source, $data = null);
    }
}
