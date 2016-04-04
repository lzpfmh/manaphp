<?php
namespace ManaPHP {

    use ManaPHP\Cache\Exception;

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
         * @var string
         */
        protected static $_defaultSerializer = 'ManaPHP\Serializer\Adapter\Serialize';


        /**
         * @var \ManaPHP\Cache\AdapterInterface
         */
        protected static $_defaultAdapter;

        /**
         * Cache constructor.
         * @param string $prefix
         * @param int $ttl
         * @param \ManaPHP\Cache\AdapterInterface $adapter
         * @param \ManaPHP\Serializer\AdapterInterface $serializer
         * @throws \ManaPHP\Cache\Exception|\ManaPHP\Di\Exception
         */
        public function __construct($prefix = '', $ttl = 3600, $adapter=null, $serializer = null)
        {
            $this->_prefix = $prefix;
            $this->_ttl = $ttl;

            if($adapter===null){
                if(self::$_defaultAdapter===null){
                    throw new Exception('please provide a valid adapter.');
                }elseif(is_object(self::$_defaultAdapter)){
                    $this->_adapter=self::$_defaultAdapter;
                }else{
                    self::$_defaultAdapter=Di::getDefault()->getShared(self::$_defaultAdapter);
                    $this->_adapter=self::$_defaultAdapter;
                }
            }else{
                $this->_adapter = $adapter;
            }
            if ($serializer === null) {
                $this->_serializer = new self::$_defaultSerializer();
            } else {
                $this->_serializer = $serializer;
            }
        }

        /**
         * @param string $serializer
         */
        public static function setDefaultSerializer($serializer)
        {
            self::$_defaultSerializer = $serializer;
        }

        /**
         * @return string
         */
        public static function getDefaultSerializer()
        {
            return self::$_defaultSerializer;
        }

        /**
         * @param \ManaPHP\Store\AdapterInterface $adapter
         */
        public static function setDefaultAdapter($adapter){
            self::$_defaultAdapter=$adapter;
        }

        /**
         * @return \ManaPHP\Store\AdapterInterface
         */
        public static function getDefaultAdapter(){
            return self::$_defaultAdapter;
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
         * @return array
         */
        public function __debugInfo()
        {
            return get_object_vars($this);
        }
    }
}