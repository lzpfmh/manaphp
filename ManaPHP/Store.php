<?php
namespace ManaPHP {

    use ManaPHP\Store\Exception;

    class Store implements StoreInterface
    {
        /**
         * @var \ManaPHP\Store\AdapterInterface
         */
        protected $_adapter;

        /**
         * @var string
         */
        protected $_prefix;

        /**
         * @var \ManaPHP\Serializer\AdapterInterface $_serializer
         */
        protected $_serializer;

        /**
         * @var string
         */
        protected static $_defaultSerializer = 'ManaPHP\Serializer\Adapter\Serialize';

        /**
         * @var \ManaPHP\Store\AdapterInterface
         */
        protected static $_defaultAdapter;

        /**
         * Store constructor.
         * @param \ManaPHP\Store\AdapterInterface $adapter
         * @param string $prefix
         * @param \ManaPHP\Serializer\AdapterInterface $serializer
         * @throws \ManaPHP\Store\Exception|\ManaPHP\Di\Exception
         */
        public function __construct($prefix = '',$adapter=null, $serializer = null)
        {
            $this->_prefix=$prefix;

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
         * @param string $id
         * @return mixed
         * @throws \ManaPHP\Store\Exception
         */
        public function get($id)
        {
            $content = $this->_adapter->get($this->_prefix . $id);
            if ($content === false) {
                return false;
            }

            return $this->_serializer->deserialize($content);
        }

        /**
         * Retrieves a value from store with a specified id.
         *
         * @param array $ids
         * @return array
         * @throws \ManaPHP\Store\Exception
         */
        public function mGet($ids)
        {
            $completeIds = [];
            foreach ($ids as $id) {
                $completeIds[] = $this->_prefix . $id;
            }

            $completeIdValues = $this->_adapter->mGet($completeIds);
            $idValues = [];
            foreach ($completeIdValues as $completeId => $value) {
                $id = substr($completeId, strlen($this->_prefix));
                if ($value === false) {
                    $idValues[$id] = $value;
                } else {
                    $idValues[$id] = $this->_serializer->deserialize($value);
                }
            }

            return $idValues;
        }

        /**
         * Stores content
         * @param string $id
         * @param mixed $value
         * @return void
         * @throws \ManaPHP\Cache\Exception
         */
        public function set($id, $value)
        {
            $this->_adapter->set($this->_prefix . $id, $this->_serializer->serialize($value));
        }


        /**
         * Stores a value identified by a id into store.
         *
         * @param array $idValues
         * @return void
         */
        public function mSet($idValues)
        {
            $completeIdValues = [];
            foreach ($idValues as $id => $value) {
                $completeIdValues[$this->_prefix . $id] = $this->_serializer->serialize($value);
            }

            $this->_adapter->mSet($completeIdValues);
        }

        /**
         * Delete content
         *
         * @param string $id
         * @void
         */
        public function delete($id)
        {
            $this->_adapter->delete($this->_prefix . $id);
        }


        /**
         * Check if id exists
         *
         * @param string $id
         * @return bool
         */
        public function exists($id)
        {
            return $this->_adapter->exists($this->_prefix . $id);
        }

        /**
         * @return \ManaPHP\Store\AdapterInterface
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