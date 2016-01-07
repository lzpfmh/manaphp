<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/20
 * Time: 16:00
 */
namespace ManaPHP\Events {

    trait EventsAware
    {
        /**
         * @var \ManaPHP\Events\Manager
         */
        protected $_trait_eventsManager = null;
        protected static $_trait_eventPeeks;

        /**
         * Attach a listener to the events manager
         *
         * @param string $event
         * @param object|callable $handler
         * @throws \ManaPHP\Events\Exception
         */
        public function attachEvent($event, $handler)
        {
            if ($this->_trait_eventsManager === null) {
                $this->_trait_eventsManager = new Manager();
            }

            $this->_trait_eventsManager->attach($event, $handler);
        }


        /**
         * Fires an event in the events manager causing that the active listeners will be notified about it
         *
         * @param string $event
         * @param object $source
         * @param mixed $data
         * @return mixed
         */
        public function fireEvent($event, $source, $data = null)
        {
            if (self::$_trait_eventPeeks !== null) {
                foreach (self::$_trait_eventPeeks as $peek) {
                    $peek($event, $source, $data);
                }
            }

            if ($this->_trait_eventsManager !== null) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                return $this->_trait_eventsManager->fire($event, $source, $data);
            }

            return null;
        }

        public static function peekEvents($peek)
        {
            if (!$peek instanceof \Closure) {
                throw new Exception('Peek is invalid: not Closure.');
            }

            if (self::$_trait_eventPeeks === null) {
                self::$_trait_eventPeeks = [$peek];
            } else {
                self::$_trait_eventPeeks[] = $peek;
            }
        }
    }
}