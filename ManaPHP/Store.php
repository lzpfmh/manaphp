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
         * Store constructor.
         * @param \ManaPHP\Store\AdapterInterface $adapter
         */
        public function __construct($adapter)
        {
            $this->_adapter = $adapter;
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
            $content = $this->_adapter->get($id);
            if ($content === false) {
                return false;
            }

            if ($content[0] === '{') {
                $value = json_decode($content, true);
                if($value===null){
                    throw new Exception('Store json_decode failed: '.json_last_error_msg());
                }
            } else {
                $value = @unserialize($content);
                if($value ===false){
                    throw new Exception('Store unserialize failed: '.error_get_last()['message']);
                }
            }

            return $value['data'];
        }

        /**
         * Retrieves a value from store with a specified id.
         *
         * @param array $ids
         * @return array
         */
        public function mGet($ids)
        {
            $idValues = $this->_adapter->mGet($ids);
            foreach ($idValues as $id => $value) {
                if($value===false){
                    $idValues[$id]=$value;
                }else{
                    if ($value[0] === '{') {
                        $content = json_decode($value, true);
                    } else {
                        $content= unserialize($value);
                    }

                    $idValues[$id]=$content['data'];
                }
            }

            return $idValues;
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
         * Stores content
         * @param string $id
         * @param mixed $value
         * @return void
         * @throws \ManaPHP\Cache\Exception
         */
        public function set($id, $value)
        {
            $packedValue=['data'=>$value];

            if ($this->_canJsonSafely($value)) {
                $content = json_encode($packedValue, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $content = serialize($packedValue);
            }

            $this->_adapter->set($id, $content);
        }


        /**
         * Stores a value identified by a id into store.
         *
         * @param array $idValues
         * @return void
         */
        public function mSet($idValues)
        {
            foreach ($idValues as $id => $value) {
                $packedValue=['data'=>$value];

                if ($this->_canJsonSafely($value)) {
                    $idValues[$id] = json_encode($packedValue, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                } else {
                    $idValues[$id] = serialize($packedValue);
                }
            }

            $this->_adapter->mSet($idValues);
        }


        /**
         * Delete content
         *
         * @param string $id
         * @void
         */
        public function delete($id)
        {
            $this->_adapter->delete($id);
        }


        /**
         * Check if id exists
         *
         * @param string $id
         * @return bool
         */
        public function exists($id)
        {
            return $this->_adapter->exists($id);
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