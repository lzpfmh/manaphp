<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/3/20
 */
namespace ManaPHP{
    class Logger
    {
        const LEVEL_OFF=0;

        const LEVEL_FATAL=10;
        const LEVEL_ERROR=20;
        const LEVEL_WARNING=30;
        const LEVEL_INFO=40;
        const LEVEL_DEBUG=50;

        const LEVEL_ALL=100;

        /**
         * @var int
         */
        protected $_level=self::LEVEL_ALL;

        /**
         * @var \ManaPHP\Logger\AdapterInterface[]
         */
        protected $_adapters=[];

        /**
         * Filters the logs sent to the handlers to be greater or equals than a specific level
         *
         * @param int $level
         * @return static
         */
        public function setLevel($level){
            $this->_level=$level;

            return $this;
        }


        /**
         * Returns the current log level
         *
         * @return int
         */
        public function getLevel(){
            return $this->_level;
        }


        /**
         * @param \ManaPHP\Logger\AdapterInterface $adapter
         * @return static
         */
        public function addAdapter($adapter){
            $this->_adapters[]=$adapter;

            return $this;
        }

        /**
         * @param int $level
         * @param string $message
         * @param array $context
         * @return static
         */
        protected function _log($level,$message, $context){
            if($level >$this->_level){
                return $this;
            }

            foreach($this->_adapters as $adapter){
                try{
                    $adapter->log($level,$message,$context);
                } catch(\Exception $e){
                    trigger_error('Logger Failed: '.$e->getMessage(), E_USER_ERROR);
                }
            }

            return $this;
        }

        /**
         * Sends/Writes a debug message to the log
         *
         * @param string $message
         * @param array $context
         * @return static
         */
        public function debug($message, $context=null){
            return $this->_log(self::LEVEL_DEBUG,$message,$context);
        }


        /**
         * Sends/Writes an info message to the log
         *
         * @param string $message
         * @param array $context
         * @return static
         */
        public function info($message, $context=null){
            return $this->_log(self::LEVEL_INFO,$message,$context);
        }


        /**
         * Sends/Writes a warning message to the log
         *
         * @param string $message
         * @param array $context
         * @return static
         */
        public function warning($message, $context=null){
            return $this->_log(self::LEVEL_WARNING,$message,$context);
        }


        /**
         * Sends/Writes an error message to the log
         *
         * @param string $message
         * @param array $context
         * @return static
         */
        public function error($message, $context=null){
            return $this->_log(self::LEVEL_ERROR,$message,$context);
        }


        /**
         * Sends/Writes a critical message to the log
         *
         * @param string $message
         * @param array $context
         * @return static
         */
        public function fatal($message, $context=null){
            return $this->_log(self::LEVEL_FATAL,$message,$context);
        }
    }
}