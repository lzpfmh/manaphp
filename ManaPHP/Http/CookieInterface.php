<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/27
 * Time: 23:06
 */
namespace ManaPHP\Http{
    /**
     * Phalcon\Http\CookieInterface
     *
     * Interface for Phalcon\Http\Cookie
     */
    interface CookieInterface
    {
        /**
         * Sets the cookie's value
         *
         * @param string $value
         * @return \ManaPHP\Http\CookieInterface
         */
        public function setValue($value);

        /**
         * Returns the cookie's value
         *
         * @param string|array $filters
         * @param string $defaultValue
         * @return mixed
         */
        public function getValue($filters = null, $defaultValue = null);

        /**
         * Sends the cookie to the HTTP client
         * @return \ManaPHP\Http\CookieInterface
         */
        public function send();

        /**
         * Deletes the cookie
         * @return \ManaPHP\Http\CookieInterface
         */
        public function delete();

        /**
         * Sets if the cookie must be encrypted/decrypted automatically
         * @param boolean $useEncryption
         * @return \ManaPHP\Http\CookieInterface
         */
        public function useEncryption($useEncryption);

        /**
         * Check if the cookie is using implicit encryption
         * @return boolean
         */
        public function isUsingEncryption();

        /**
         * Sets the cookie's expiration time
         * @param int $expire
         * @return \ManaPHP\Http\CookieInterface
         */
        public function setExpiration($expire);


        /**
         * Sets the cookie's expiration time
         * @param string $path
         * @return \ManaPHP\Http\CookieInterface
         */
        public function setPath($path);

        /**
         * Returns the current cookie's name
         * @return string
         */
        public function getName();


        /**
         * Sets the domain that the cookie is available to
         * @param string $domain
         * @return \ManaPHP\Http\CookieInterface
         */
        public function setDomain($domain);

        /**
         * Sets if the cookie must only be sent when the connection is secure (HTTPS)
         * @param boolean $secure
         * @return \ManaPHP\Http\CookieInterface
         */
        public function setSecure($secure);

        /**
         * Sets if the cookie is accessible only through the HTTP protocol
         * @param boolean $httpOnly
         * * @return \ManaPHP\Http\CookieInterface
         */
        public function setHttpOnly($httpOnly);

    }

}