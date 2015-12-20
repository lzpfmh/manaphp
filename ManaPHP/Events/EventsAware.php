<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/20
 * Time: 16:00
 */
namespace ManaPHP\Events {
    trait EventsAware{
        /**
         * @var \ManaPHP\Events\Manager
         */
        protected $_trait_eventsManager=null;
        protected static $_trait_eventPeeker;
        /**
         * Attach a listener to the events manager
         *
         * @param string $event
         * @param object|callable $handler
         */
        public function attachEvent($event, $handler){
            if($this->_trait_eventsManager ===null){
                $this->_trait_eventsManager =new \ManaPHP\Events\Manager();
            }

            $this->_trait_eventsManager->attach($event,$handler);
        }


        /**
         * Fires an event in the events manager causing that the active listeners will be notified about it
         *
         * @param string $event
         * @param object $source
         * @param mixed  $data
         */
        public function fireEvent($event, $source, $data=null){
            if(self::$_trait_eventPeeker !=null){
                foreach(self::$_trait_eventPeeker as $peeker){
                    $peeker($event,$source,$data);
                }
            }

            if($this->_trait_eventsManager !==null){
                return $this->_trait_eventsManager->fire($event,$source,$data);
            }
        }

        public static function peekEvents($peeker){
            if(!$peeker instanceof \Closure){
                throw new Exception('Peeker is not Closure.');
            }

            if(self::$_trait_eventPeeker===null){
                self::$_trait_eventPeeker=[$peeker];
            }else{
                self::$_trait_eventPeeker[]=$peeker;
            }
        }
    }
}