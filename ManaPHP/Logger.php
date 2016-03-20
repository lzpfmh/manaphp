<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/3/20
 */
namespace ManaPHP{
    abstract class Logger
    {
        const LEVEL_FATAL=1;
        const LEVEL_ERROR=2;
        const LEVEL_WARNING=3;
        const LEVEL_INFO=4;
        const LEVEL_DEBUG=5;

        protected $_level=self::LEVEL_DEBUG;

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

        abstract function _log($type,$message, $context);


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