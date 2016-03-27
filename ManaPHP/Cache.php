<?php
namespace ManaPHP {

    class Cache implements CacheInterface
    {
        /**
         * @var \ManaPHP\Cache\AdapterInterface
         */
        protected $_adapter;

        /**
         * @var string
         */
        protected $_prefix;

        /**
         * @var int
         */
        protected $_ttl;


        /**
         * Cache constructor.
         * @param \ManaPHP\Cache\AdapterInterface $adapter
         * @param string $prefix
         * @param int $ttl
         */
        public function __construct($adapter, $prefix = '', $ttl = 3600)
        {
            $this->_adapter = $adapter;
            $this->_adapter->setPrefix($prefix);
            $this->_prefix = $prefix;
            $this->_ttl = $ttl;
        }

        /**
         * Fetch content
         *
         * @param string $key
         * @return mixed
         * @throws \ManaPHP\Cache\Exception
         */
        public function get($key)
        {
            $content = $this->_adapter->get($key);
            if ($content === false) {
                return false;
            }

            if ($content[0] === '{') {
                $value = json_decode($content, true);
            } else {
                $value=unserialize($content);
            }

            return $value['data'];
        }

        /**
         * @param mixed $value
         * @return bool
         */
        protected function _canJsonSafely($value)
        {
            if (is_scalar($value) || $value === null) {
                return true;
            } elseif (is_array($value)) {
                foreach ($value as $v) {
                    if (!$this->_canJsonSafely($v)) {
                        return false;
                    }
                }
            } else {
                return false;
            }

            return true;
        }

        /**
         * Caches content
         * @param string $key
         * @param mixed $value
         * @param int $ttl
         * @return void
         * @throws \ManaPHP\Cache\Exception
         */
        public function set($key, $value, $ttl = null)
        {
            $packedValue=['ttl'=>$ttl?:$this->_ttl,'data'=>$value];
            if($this->_canJsonSafely($value)){
                $content=json_encode($packedValue, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $content = serialize($packedValue);
            }

            $this->_adapter->set($key, $content, $ttl);
        }


        /**
         * Delete content
         *
         * @param string $key
         * @void
         */
        public function delete($key)
        {
            $this->_adapter->delete($key);
        }


        /**
         * Check if key exists
         *
         * @param string $key
         * @return bool
         */
        public function exists($key)
        {
            return $this->_adapter->exists($key);
        }

        /**
         * @return \ManaPHP\Cache\AdapterInterface
         */
        public function getAdapter(){
            return $this->_adapter;
        }

        /**
         * @return array
         */
        public function __debugInfo()
        {
            return get_object_vars($this);
        }
    }
}