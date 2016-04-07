<?php
namespace ManaPHP {

    use ManaPHP\Serializer\Adapter\JsonPhp;

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
         * @var Serializer\AdapterInterface;
         */
        protected $_serializer;

        /**
         * Cache constructor.
         * @param string $prefix
         * @param int $ttl
         * @param string|\ManaPHP\Cache\AdapterInterface $adapter
         * @param \ManaPHP\Serializer\AdapterInterface $serializer
         * @throws \ManaPHP\Cache\Exception|\ManaPHP\Di\Exception
         */
        public function __construct($prefix = '', $ttl = 3600, $adapter = null, $serializer = null)
        {
            $this->_prefix = $prefix;
            $this->_ttl = $ttl;

            if ($adapter === null) {
                $adapter = 'defaultCacheAdapter';
            }

            $this->_adapter = is_string($adapter) ? Di::getDefault()->getShared($adapter) : $adapter;
            $this->_serializer = $serializer ?: new JsonPhp();
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
            $data = $this->_adapter->get($this->_prefix . $key);
            if ($data === false) {
                return false;
            } else {
                return $this->_serializer->deserialize($data);
            }
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
            $this->_adapter->set($this->_prefix . $key, $this->_serializer->serialize($value), $ttl);
        }


        /**
         * Delete content
         *
         * @param string $key
         * @void
         */
        public function delete($key)
        {
            $this->_adapter->delete($this->_prefix . $key);
        }


        /**
         * Check if key exists
         *
         * @param string $key
         * @return bool
         */
        public function exists($key)
        {
            return $this->_adapter->exists($this->_prefix . $key);
        }

        /**
         * @return \ManaPHP\Cache\AdapterInterface
         */
        public function getAdapter()
        {
            return $this->_adapter;
        }

        /**
         * @return \ManaPHP\Serializer\AdapterInterface
         */
        public function getSerializer()
        {
            return $this->_serializer;
        }

        /**
         * @return array
         */
        public function __debugInfo()
        {
            return get_object_vars($this) ?: [];
        }
    }
}