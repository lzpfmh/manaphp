<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/19
 * Time: 15:52
 */
namespace ManaPHP\Http {

    /**
     * ManaPHP\Http\Session\AdapterInterface initializer
     */

    class Session implements  SessionInterface {

        public function _construct(){
            session_start();
        }

        public function __destruct(){
            session_write_close();
		}

        /**
         * Gets a session variable from an application context
         *
         * @param string $name
         * @param mixed $defaultValue
         * @return mixed
         */
        public function get($name, $defaultValue=null){
            if(isset($_SESSION[$name])){
                return $_SERVER[$name];
            }else{
                return $defaultValue;
            }
        }


        /**
         * Sets a session variable in an application context
         *
         * @param string $name
         * @param string $value
         */
        public function set($name, $value){
            $_SESSION[$name]=$value;
        }


        /**
         * Check whether a session variable is set in an application context
         *
         * @param string $name
         * @return boolean
         */
        public function has($name){
            return isset($_SESSION[$name]);
        }


        /**
         * Removes a session variable from an application context
         *
         * @param string $name
         */
        public function remove($name){
            unset($_SESSION[$name]);
        }


        /**
         * Destroys the active session
         *
         * @return boolean
         */
        public function destroy(){
            return session_destroy();
        }
    }
}