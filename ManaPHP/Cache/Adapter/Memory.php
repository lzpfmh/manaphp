<?php
namespace ManaPHP\Cache\Adapter {

    use ManaPHP\Cache;
    use ManaPHP\Cache\AdapterInterface;

    class Memory implements AdapterInterface
    {
        /**
         * @var array
         */
        protected $_data = [];

        /**
         * @var string
         */
        protected $_prefix = '';

        public function get($key)
        {
            $key = $this->_prefix . $key;

            if (isset($this->_data[$key])) {
                if ($this->_data[$key]['deadline'] >= time()) {
                    return $this->_data[$key]['data'];
                } else {
                    unset($this->_data[$key]);
                }
            } else {
                return false;
            }
        }

        public function set($key, $value, $ttl)
        {
            $key = $this->_prefix . $key;

            $this->_data[$key] = ['deadline' => time() + $ttl, 'data' => $value];
        }

        public function delete($key)
        {
            $key = $this->_prefix . $key;

            unset($this->_data[$key]);
        }

        public function exists($key)
        {
            $key = $this->_prefix . $key;

            return isset($this->_data[$key]) && $this->_data[$key]['deadline'] >= time();
        }

        public function setPrefix($prefix)
        {
            $this->_prefix = $prefix;

            return $this;
        }
    }
}